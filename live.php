<DOCTYPE html/>
<html>
    <head>
        <title>Radio Generation: Live TV</title>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
        <link as="image" rel="preload" href="https://www.radiogeneration.it/assets/social-media-img.jpg" fetchpriority="high"/>
        <meta property="og:title" content="Radio Generation" />
        <meta property="og:description" content="Diretta TV" />
        <meta property="og:image" content="https://www.radiogeneration.it/assets/social-media-img.jpg" />
        <meta property="og:url" content="https://www.radiogeneration.it" />
        <meta name="theme-color" content="#121212"/>
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
        <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                background: #101010;
            }
            
            video {
                width: 100%;
                height: 100%;
                border: 8px solid #00000091;
                border-radius: 48px;
                box-shadow: 0 0 42px 10px rgba(0, 0, 0, .3);
            }
            
            .live-video-container {
                height: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            
            .video-element {
                height: calc(100vh - 67px);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                background: #000;
                border-radius: 48px;
                transition: height 200ms ease-in-out;
            }
        </style>

    </head>
    <body>
        <div class="hls-live-container offline" --live-container>                         
            <div class="hls-live-streaming">
                
                <div class="tv-title-status" style="display: none">
                    <div class="hls-live-title-container">
                        <div class="hls-live-title">
                            <div class="live-status-ball"></div>
                            <img src="https://www.radiogeneration.it/assets/social-media-img.jpg" />
                        </div>
                    </div>
                    
                   
                </div>
                
                <div class="video-wrapper">
                    <!--
                    <div class="hls-image-set">
                        <div class="ls-left-image"><img src="https://www.radiogeneration.it/assets/left-image.jpg"/></div>
                        <div class="ls-right-image"><img src="https://www.radiogeneration.it/assets/right-image.jpg"/></div>
                    </div>
                -->  
                    
                    <div class="live-video-container">
                        <!--
                        <div class="hls-image-set">
                            <div class="ls-left-image"><img src="https://www.radiogeneration.it/assets/left-image.jpg"/></div>
                            <div class="ls-right-image"><img src="https://www.radiogeneration.it/assets/right-image.jpg"/></div>
                        </div>
                        -->
                        <div class="video-element">

                            <div class="ls-video-play-btn" --ls-play-btn>
                                <div class="ls-play-btn-icn"></div>
                            </div>

                            <video id="video-live" controls="false" autoplay width="1280" height="720" src="https://www.radiogeneration.it/cdn/storage/file/tv.mp4"></video>
                        </div>
                    </div>
                    
                </div>
                

            </div>
        </div>
        <script>
            (() => {
                const videoElement           = document.querySelector(".video-element");
                
                window.hls                   = new Hls();
                window.videoStreamingElement = document.getElementById('video-live');
                window.streamStatus          = "offline";
                window.isStreamingEnabled    = true;
    
    
                const videoPlayBtn  = document.querySelector("[--ls-play-btn]");
                const liveContainer = document.querySelector("[--live-container]");
    
                videoPlayBtn.addEventListener("click", () => {
                    if (window.videoStreamingElement.paused)
                        window.videoStreamingElement.play();
                    else
                        window.videoStreamingElement.pause();
                    
                    // window.videoStreamingElement.currentTime = window.videoStreamingElement.duration;
                });
    
    
                window.videoStreamingElement.addEventListener("progress", () => {
                   
    
                });
    
                window.videoStreamingElement.addEventListener("playing", () => {
                    videoPlayBtn.classList.add("l-playing");
                });
    
                window.videoStreamingElement.addEventListener("pause", () => {
                    videoPlayBtn.classList.remove("l-playing");
                });
    
    
                window.onStreamStatusChange = () => {
    
                    if (!window.isStreamingEnabled)
                        return;
    
                    if (window.streamStatus == "online") {
                        liveContainer.classList.remove("offline");
                        window.videoStreamingElement.muted = false;
    
    
                        window.hls.loadSource('https://www.radiogeneration.it/api/video-live/BAyCE-DAB2w-AxxCZ-B6d54-4BECY.m3u8');
                        window.hls.attachMedia(window.videoStreamingElement);
    
                        const playPromise = window.videoStreamingElement.play();
                        if (playPromise) {
                            playPromise.then(e => {
                                videoPlayBtn.classList.remove("show-pb");
                            }).catch(ex => {
                                videoPlayBtn.classList.add("show-pb");
                            });
                        }
    
                        
                    } else {
                        liveContainer.classList.add("offline");
                        window.hls.detachMedia(window.videoStreamingElement);
                        window.videoStreamingElement.muted    = true;                    
                        window.videoStreamingElement.src      = "https://www.radiogeneration.it/cdn/storage/file/tv.mp4";
    
                        videoPlayBtn.classList.remove("show-pb");
    
                    }
                };
    
                (() => {
                })();
    
                const checkStreamingStatus = async () => {
    
                    try {
                        const response = await fetch("https://www.radiogeneration.it/api/stream-status");
                        const data     = await response.json();
    
                        if (data.status !== window.streamStatus) {
                            window.streamStatus = data.status;
                            window.onStreamStatusChange();
                        }
    
                        setTimeout(checkStreamingStatus, 5000);    
                    } catch(ex) {
                        console.log("checkStreamingStatus() Exception caught: ", ex);
                        setTimeout(checkStreamingStatus, 5000);
                    }
                };
                
                checkStreamingStatus();
                
                
                screen.orientation.addEventListener("change", (event) => {
                    const ww = window.innerWidth;
                    const wh = window.innerHeight;

                    const type = event.target.type;
                    const angle = event.target.angle;
                    if (type.startsWith("landscape")) {
                        newHeight = wh - 67;
                    } else {
                        const {width, height} = window.videoStreamingElement;
                        const vAr = width / height;
                        newHeight = ww / vAr;
                    }
                
                    videoElement.style.height = `${newHeight}px`;
                    
                });
                
                 
                const handleWinResize = () => {
                    const ww = window.innerWidth;
                    const wh = window.innerHeight;
                    
                    const ratio = ww/wh;
                    let newHeight = 0;
                    let newWidth  = 0;
                    
                    if (ratio < 1) {
                        const {width, height} = window.videoStreamingElement;
                        const vAr = width / height;
                        newHeight = ww / vAr;
                        newWidth  = "100%";
                    } else {
                       newHeight = wh - 67;
                       newWidth  = "80%";
                    }

                    videoElement.style.width = `${newWidth}`;
                    videoElement.style.height = `${newHeight}px`;

                };

                window.addEventListener("resize", () => {
                    handleWinResize();
                });
                
                handleWinResize();
            
            })();
        </script>
    </body>
</html>