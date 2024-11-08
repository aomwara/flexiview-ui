import cv2
import mediapipe as mp
from cvzone.FaceMeshModule import FaceMeshDetector
import numpy as np
from PIL import ImageFont, ImageDraw, Image
import time
import requests
import json
from datetime import datetime

# ตั้งค่า Mediapipe และ OpenCV สำหรับตรวจจับท่าทาง
mp_drawing = mp.solutions.drawing_utils
mp_drawing_styles = mp.solutions.drawing_styles
mp_pose = mp.solutions.pose

# ตั้งค่า FaceMeshDetector สำหรับตรวจจับใบหน้าและคำนวณระยะห่าง
cap1 = cv2.VideoCapture(0)  # กล้องโน๊ตบุ๊ค
cap2 = cv2.VideoCapture(1)  # กล้อง USB
detector = FaceMeshDetector(maxFaces=1)

# ค่าคงที่สำหรับการวัดระยะห่าง
KNOWN_DISTANCE = 50  # ระยะที่ทราบค่า (เช่น 50 ซม.)
KNOWN_WIDTH = 6.3  # ความกว้างระหว่างดวงตาที่ทราบค่า (เช่น 6.3 ซม.)
FOCAL_LENGTH = 700  # กำหนด focal length คงที่ที่ 700

# ฟังก์   ันคำนวณระยะห่าง
def distance_finder(focal_length, real_face_width, face_width_in_frame):
    distance = (real_face_width * focal_length) / face_width_in_frame
    return distance

# ฟังก์ชันคำนวณมุม
def calculate_angle(a, b, c):
    a = np.array(a)
    b = np.array(b)
    c = np.array(c)

    radians = np.arctan2(c[1] - b[1], c[0] - b[0]) - np.arctan2(a[1] - b[1], a[0] - b[0])
    angle = np.abs(radians * 180.0 / np.pi)

    if angle > 180.0:
        angle = 360 - angle
    
    return angle

# โหลดฟอนต์ภาษาไทย
fontpath = "angsana.ttc"
font = ImageFont.truetype(fontpath, 32)
 
# ค่าเริ่มต้นสำหรับการตรวจจับท่าทาง
NECK_MIN = 145
NECK_MAX = 180
SHOULDER_MIN = 150
SHOULDER_MAX = 180
BACK_MIN = 55
BACK_MAX = 170

# ตัวแปรสำหรับการจับเวลา
correct_time = 0
incorrect_time = 0
start_time = None
last_status_change_time = None
previous_status = None
neck_start_time = back_start_time = shoulder_start_time = None
neck_correct_time = back_correct_time = shoulder_correct_time = 0
neck_incorrect_time = back_incorrect_time = shoulder_incorrect_time = 0

# Add after other global variables
last_api_send_time = 0
API_SEND_INTERVAL = 120  # seconds

# Add after other timer variables
distance_incorrect_time = 0
distance_start_time = None

# ฟังก์ชันเพื่อแปลงเวลาเป็น ชั่วโมง:นาที:วินาที
def format_time(seconds):
    hours = int(seconds // 3600)
    minutes = int((seconds % 3600) // 60)
    seconds = int(seconds % 60)
    return f"{hours:02}:{minutes:02}:{seconds:02}"

# เริ่มตรวจจับท่าทางและวัดระยะห่าง
with mp_pose.Pose(min_detection_confidence=0.5, min_tracking_confidence=0.5) as pose:
    while cap1.isOpened() and cap2.isOpened():
        ret1, frame1 = cap1.read()
        ret2, frame2 = cap2.read()
        
        if not ret1 or not ret2:
            print("ไม่สามารถอ่านเฟรมจากกล้องได้")
            break

        # การแสดงผลระยะทางและข้อความถูกต้อง/ไม่ถูกต้องบนกล้องตัวที่ 1
        image1, faces = detector.findFaceMesh(frame1, draw=True)
        distance_status = ""
        
        if faces:
            face = faces[0]
            left_eye = face[145]
            right_eye = face[374]

            # คำนวณระยะห่างระหว่างดวงตาในหน่วยพิกเซล
            face_width_in_frame = np.linalg.norm(np.array(left_eye) - np.array(right_eye))
            distance = distance_finder(FOCAL_LENGTH, KNOWN_WIDTH, face_width_in_frame)
                
            # กำหนดข้อความระยะห่างและสถานะ
            distance_text = f"Distance: {int(distance)} cm"
            if 50 <= distance <= 70:
                distance_status = "correct distance!"
                distance_correct = True
                color = (0, 255, 0)
                distance_start_time = None
            else:
                distance_status = "incorrect distance!"
                distance_correct = False
                color = (255, 0, 0)
                # Track incorrect distance time
                current_time = time.time()
                if distance_start_time is None:
                    distance_start_time = current_time
                distance_incorrect_time += current_time - distance_start_time
                distance_start_time = current_time

            # Add incorrect distance timer display
            incorrect_distance_text = f"Incorrect Distance Time: {format_time(distance_incorrect_time)}"
            
            img_pil = Image.fromarray(cv2.cvtColor(image1, cv2.COLOR_BGR2RGB))
            draw = ImageDraw.Draw(img_pil)

            status_text_position = (10, image1.shape[0] - 50)
            distance_text_position = (10, status_text_position[1] - 40)
            timer_text_position = (10, status_text_position[1] - 80)
            
            draw.text(distance_text_position, distance_text, font=font, fill=color)
            draw.text(status_text_position, distance_status, font=font, fill=color)
            if not distance_correct:
                draw.text(timer_text_position, incorrect_distance_text, font=font, fill=color)

            image1 = cv2.cvtColor(np.array(img_pil), cv2.COLOR_RGB2BGR)
            cv2.circle(image1, left_eye, 5, (255, 0, 0), -1)
            cv2.circle(image1, right_eye, 5, (255, 0, 0), -1)

        # แสดงผลกล้องตัวที่ 1
        cv2.imshow("Notebook Camera - Distance Measurement", image1)

        # การแสดงผลข้อมูลการตรวจจับท่าทางบนกล้องตัวที่ 2
        image2 = cv2.cvtColor(frame2, cv2.COLOR_BGR2RGB)
        image2.flags.writeable = False
        results = pose.process(image2)
        image2.flags.writeable = True
        image2 = cv2.cvtColor(image2, cv2.COLOR_RGB2BGR)

        posture_correct = False
        posture_status = ""
        
        if results.pose_landmarks:
            landmarks = results.pose_landmarks.landmark
            ear = [landmarks[mp_pose.PoseLandmark.RIGHT_EAR.value].x,
                   landmarks[mp_pose.PoseLandmark.RIGHT_EAR.value].y]
            shoulder = [landmarks[mp_pose.PoseLandmark.RIGHT_SHOULDER.value].x,
                        landmarks[mp_pose.PoseLandmark.RIGHT_SHOULDER.value].y]
            hip = [landmarks[mp_pose.PoseLandmark.RIGHT_HIP.value].x,
                   landmarks[mp_pose.PoseLandmark.RIGHT_HIP.value].y]
            knee = [landmarks[mp_pose.PoseLandmark.RIGHT_KNEE.value].x,
                    landmarks[mp_pose.PoseLandmark.RIGHT_KNEE.value].y]

            neck_angle = calculate_angle(ear, shoulder, hip)
            back_angle = calculate_angle(shoulder, hip, knee)
            shoulder_angle = calculate_angle(hip, shoulder, [shoulder[0], shoulder[1] - 0.1])

            neck_status = "Correct" if NECK_MIN <= neck_angle <= NECK_MAX else "Incorrect"
            back_status = "Correct" if BACK_MIN <= back_angle <= BACK_MAX else "Incorrect"
            shoulder_status = "Correct" if SHOULDER_MIN <= shoulder_angle <= SHOULDER_MAX else "Incorrect"

            # ตรวจสอบสถานะการจัดท่าทางท     งหมดและจับเวลาถูกต้อง
            if neck_status == "Correct":
                if neck_start_time is None:
                    neck_start_time = time.time()
                neck_correct_time += time.time() - neck_start_time
                neck_start_time = time.time()
            else:
                if neck_start_time is None:
                    neck_start_time = time.time()
                neck_incorrect_time += time.time() - neck_start_time
                neck_start_time = time.time()

            if back_status == "Correct":
                if back_start_time is None:
                    back_start_time = time.time()
                back_correct_time += time.time() - back_start_time
                back_start_time = time.time()
            else:
                if back_start_time is None:
                    back_start_time = time.time()
                back_incorrect_time += time.time() - back_start_time
                back_start_time = time.time()

            if shoulder_status == "Correct":
                if shoulder_start_time is None:
                    shoulder_start_time = time.time()
                shoulder_correct_time += time.time() - shoulder_start_time
                shoulder_start_time = time.time()
            else:
                if shoulder_start_time is None:
                    shoulder_start_time = time.time()
                shoulder_incorrect_time += time.time() - shoulder_start_time
                shoulder_start_time = time.time()

            # กำหนดสีตามสถานะของแต่ละมุม
            neck_color = (0, 255, 0) if neck_status == "Correct" else (0, 0, 255)
            back_color = (0, 255, 0) if back_status == "Correct" else (0, 0, 255)
            shoulder_color = (0, 255, 0) if shoulder_status == "Correct" else (0, 0, 255)

            # แสดงข้อมูลท่าทางบนกล้องตัวที่ 2
            text_y_position = 70
            text_spacing = 40
            cv2.putText(frame2, f'Neck Angle: {int(neck_angle)}° ({neck_status})', (10, text_y_position), cv2.FONT_HERSHEY_SIMPLEX, 0.8, neck_color, 2)
            text_y_position += text_spacing
            cv2.putText(frame2, f'Back Angle: {int(back_angle)}° ({back_status})', (10, text_y_position), cv2.FONT_HERSHEY_SIMPLEX, 0.8, back_color, 2)
            text_y_position += text_spacing
            cv2.putText(frame2, f'Shoulder Angle: {int(shoulder_angle)}° ({shoulder_status})', (10, text_y_position), cv2.FONT_HERSHEY_SIMPLEX, 0.8, shoulder_color, 2)

            # แสดงเวลาการจัดท่าที่ถูกต้อง
            text_y_position += text_spacing
            cv2.putText(frame2, f"Time Neck Correct: {format_time(neck_correct_time)}", (10, text_y_position), cv2.FONT_HERSHEY_SIMPLEX, 0.6, neck_color, 2)
            text_y_position += text_spacing
            cv2.putText(frame2, f"Time Back Correct: {format_time(back_correct_time)}", (10, text_y_position), cv2.FONT_HERSHEY_SIMPLEX, 0.6, back_color, 2)
            text_y_position += text_spacing
            cv2.putText(frame2, f"Time Shoulder Correct: {format_time(shoulder_correct_time)}", (10, text_y_position), cv2.FONT_HERSHEY_SIMPLEX, 0.6, shoulder_color, 2)

            # Add display of incorrect times
            text_y_position += text_spacing
            cv2.putText(frame2, f"Time Neck Incorrect: {format_time(neck_incorrect_time)}", (10, text_y_position), cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 0, 255), 2)
            text_y_position += text_spacing
            cv2.putText(frame2, f"Time Back Incorrect: {format_time(back_incorrect_time)}", (10, text_y_position), cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 0, 255), 2)
            text_y_position += text_spacing
            cv2.putText(frame2, f"Time Shoulder Incorrect: {format_time(shoulder_incorrect_time)}", (10, text_y_position), cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 0, 255), 2)

            # ตรวจสอบสถานะการจัดท่าทางรวม
            posture_correct = neck_status == "Correct" and back_status == "Correct" and shoulder_status == "Correct"
            final_status = "ALL POSTURE CORRECT" if posture_correct and distance_correct else "ALL POSTURE INCORRECT"
            final_color = (0, 255, 0) if posture_correct and distance_correct else (0, 0, 255)

            # อัพเดทเวลาการจัดท่ารวมและแสดงข้อมูล
            current_time = time.time()
            if posture_correct and distance_correct:
                if last_status_change_time is not None:
                    correct_time += current_time - last_status_change_time
            else:
                if last_status_change_time is not None:
                    incorrect_time += current_time - last_status_change_time
            last_status_change_time = current_time

            if start_time is None:
                start_time = time.time()
                last_status_change_time = start_time

            total_time = format_time(current_time - start_time)
            correct_display_time = format_time(correct_time)
            incorrect_display_time = format_time(incorrect_time)
            
            position_x = 10
            position_y = frame2.shape[0] - 60
            cv2.putText(frame2, final_status, (position_x, position_y), cv2.FONT_HERSHEY_SIMPLEX, 1, final_color, 2)
            cv2.putText(frame2, f"Time: {total_time}", (10, position_y + 30), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 0), 2)
            cv2.putText(frame2, f"Correct: {correct_display_time}", (10, position_y + 60), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 0), 2)
            cv2.putText(frame2, f"Incorrect: {incorrect_display_time}", (10, position_y + 90), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 2)

            # แสดงเส้นโครงร่าง
            mp_drawing.draw_landmarks(
                frame2, 
                results.pose_landmarks, 
                mp_pose.POSE_CONNECTIONS,
                landmark_drawing_spec=mp_drawing_styles.get_default_pose_landmarks_style()
            )
            
            # Add API sending logic
            current_time = time.time()
            if current_time - last_api_send_time >= API_SEND_INTERVAL:
                try:
                    url = f' https://flexiview.photha.online/api/send-data.php?Time_Neck_Incorrect={format_time(neck_correct_time)}&Time_Back_Incorrect={format_time(back_incorrect_time)}&Time_Shoulder_Incorrect={format_time(shoulder_incorrect_time)}&neck_status={neck_status}&back_status={back_status}&shoulder_status={shoulder_status}&distance_status={distance_status if "distance_status" in locals() else "Unknown"}&total_correct_time={correct_time}&total_incorrect_time={incorrect_time}'
 
                    
                    response = requests.get(url)
                    
                    if response.status_code == 200:
                        print("Data sent successfully to API")
                    else:
                        print(f"Failed to send data to API")
                        
                except Exception as e:
                    print(f"Error sending data to API: {str(e)}")
                
                last_api_send_time = current_time

        # แ  ดงผลกล้องตัวที่ 2
        cv2.imshow("USB Camera - Posture Detection", frame2)

        # กด 'q' เพื่อออกจากโปรแกรม
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    cap1.release()
    cap2.release()
    cv2.destroyAllWindows()
