<style>
.container-fluid {
    max-width: 1200px;
    margin: 0 auto;
}

.video-section {
    margin-top: 20px;
}

.video-section h3 {
    margin-bottom: 10px;
    color: #333;
}

.video-section ul {
    margin-bottom: 20px;
    padding-left: 20px;
    color: #555;
}

.video-section li {
    margin-bottom: 5px;
}

.video-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

.video-container {
    flex: 1 1 300px;
    max-width: 560px;
}

.video-container iframe {
    width: 100%;
    height: 315px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

/* Responsive design for smaller screens */
@media (max-width: 768px) {
    .video-container iframe {
        height: 250px;
    }
}
</style>

<div class="container-fluid p-4">
    <h2>Welcome to Flexiview</h2>
    <hr>

    <div class="video-section">
        <h3>วีดีโอแนะนำท่ากายบริหาร</h3>
        <ul>
            <li>10 นาที สำหรับคนนั่งทำงานน้อยกว่า 1 ชั่วโมง</li>
            <li>20 นาที สำหรับคนนั่งทำงานมากกว่า 1 ชั่วโมง</li>
            <li>30 นาที สำหรับคนนั่งทำงานมากกว่า 5 ชั่วโมง</li>
        </ul>

        <div class="video-wrapper">
            <div class="video-container">
                <h4>10 นาที</h4>
                <iframe src="https://www.youtube.com/embed/o-imrB4hEJI" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            </div>
            <div class="video-container">
                <h4>20 นาที</h4>
                <iframe src="https://www.youtube.com/embed/rKVfi9Uaj78" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            </div>
            <div class="video-container">
                <h4>30 นาที</h4>
                <iframe src="https://www.youtube.com/embed/096G--eirXo" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>