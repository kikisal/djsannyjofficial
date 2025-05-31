<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/core.php';

$videoFormats = [
    "mp4",
    "avi",
    "mpeg"
];

$bannerImages = [
    "https://www.radiogeneration.it/assets/image-slider/img5.jfif",
    "https://www.radiogeneration.it/assets/image-slider/ross.png",
    "https://www.radiogeneration.it/assets/image-slider/img2.jfif",
    "https://www.radiogeneration.it/assets/image-slider/img3.jfif",
    "https://www.radiogeneration.it/assets/image-slider/img4.jfif",
    "https://www.radiogeneration.it/assets/image-slider/img6.jfif",
    "https://www.radiogeneration.it/assets/image-slider/img7.jfif",
    "https://www.radiogeneration.it/assets/image-slider/img8.jpg",
    "https://www.radiogeneration.it/assets/image-slider/img10.jpg",
    "https://www.radiogeneration.it/assets/image-slider/img11.jpg",
    "https://www.radiogeneration.it/assets/image-slider/3f60d47f-851e-4230-b61d-6d5dd035e529.jpeg"
];

$siaeLicence = "202500000333";

if ($db->connect()) {
    $conn = $db->getConnection();
    try {
        $stmt = $conn->prepare("SELECT * FROM `banner_images` ORDER BY `order` ASC");
        
        if ($stmt->execute()) {
            
            $bannerImages = [];

            $elements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($elements as $element) {
                $url = $element["image_url"];
                if (empty($url))
                    continue;

                $bannerImages[] = $url; 
            }
        }

    } catch (\PDOException $ex) {
        // ignore
    }
}

?>
<!DOCTYPE html>
<html lang="it">
<?php require_once __DIR__ . "/components/head.php"; ?>

<body opacity-animation class="state-disappear" use-img-animation>
    
    <div class="rg-overlay" id="rg-sharePopup">
      <div class="rg-popup">
        <h2>Condividi il link</h2>
        <input type="text" id="rg-shareUrl" readonly value="https://generationtv.it/live">
        <div class="rg-buttons">
          <button class="rg-copy" onclick="rgCopyUrl()">Copia</button>
          <button class="rg-close" onclick="rgClosePopup()">Chiudi</button>
        </div>
      </div>
    </div>
    
    <div id="rg-snackbar" class="rg-snackbar">Link copiato con successo!</div>
    <iframe --radio-ltmr-frame style="display: none" src="<?= OPEN_FIREWALL_SERVICE_LINK; ?>" frameborder="0"></iframe>
    
    <?php require_once __DIR__ . "/components/mobile-header.php"; ?>
    <?php require_once __DIR__ . "/components/header.php"; ?>
    <div class="content-wrapper content-shadow">
    <div class="slider-wrapper">
            <div class="content-wrapper"></div>
            <div class="image-slider" --image-slider-widget>
                <div class="image-slider-container">
                    <div class="image-slider-controller">
                        <div class="image-slider-align" --slider-items>
                            <?php
                            foreach ($bannerImages as $image) {
                                
                                $resUrl = $image;
                                $tokens = explode(".", $resUrl);
                                $format = @$tokens[count($tokens) - 1];

                                if (in_array($format, $videoFormats)) {
                                    echo  "<div class=\"image-slider-item\"><video class=\"video-banner-viewer\" src=\"" . $image . "\" loop muted controls=\"false\" autoplay=\"true\" width=\"640\" height=\"360\"></video></div>";
                                } else {
                                    echo "<div class=\"image-slider-item\"><img src=\"" . $image . "\" alt=\"\"/></div>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
        <style>
            .sanny-tv-image {
                width: 500px;
                background: none;
            }

            @media screen and (max-width: 800px) {
                .sanny-tv-image {                    
                    width: 85vw;
                    margin-bottom: 9px;
                    margin-top: 22px;
                }
            }
        </style>
        <div class="hls-live-container offline" --live-container>
            <h1 class="section-title gray-bg text-center" style="
            color: #000;
            display: flex;
            color: #000;
            justify-content: center;
            align-items: center;
            gap: 16px;"><img class="sanny-tv-image" src="<?= SITE_URL; ?>/assets/djsannyj/tv-label-nostar2.png?v=6"/><div class="live-recording live"></div></h1>
            <div class="hls-live-streaming">
                
                <div class="tv-title-status" style="display: none">
                    <div class="hls-live-title-container">
                        <div class="hls-live-title">
                            <div class="live-status-ball"></div>
                            <img src="<?= SITE_URL; ?>/assets/gen-tv.png" />
                        </div>
                    </div>
                    <div class="video-text digital-text" style="text-transform: uppercase">
                        <span>Guarda la nostra tv 24h su 24!</span>
                    </div>
                    <div class="video-text" --ls-offline-text style="
                    text-transform: uppercase;
                    font-size: 18px;
                    font-family: 'Inria Sans', sans-serif;
                    color: #ff4949;
                    text-shadow: 0 0 16px black;">
                        <span>Attualmente offline</span>
                    </div>
                    
                </div>
                
                <div class="video-wrapper">
                    <!--
                    <div class="hls-image-set">
                        <div class="ls-left-image"><img src="<?= SITE_URL; ?>/assets/left-image.jpg"/></div>
                        <div class="ls-right-image"><img src="<?= SITE_URL; ?>/assets/right-image.jpg"/></div>
                    </div>
                -->  
                    
                    <div class="live-video-container">
                        <!--
                        <div class="hls-image-set">
                            <div class="ls-left-image"><img src="<?= SITE_URL; ?>/assets/left-image.jpg"/></div>
                            <div class="ls-right-image"><img src="<?= SITE_URL; ?>/assets/right-image.jpg"/></div>
                        </div>
                        -->
                        <div class="video-element">
                            <div class="video-live-controls"><div class="video-live-btn" title="Copia link"><button class="rg-open-popup"><div class="lvbutton-img"></div></button></div></div>

                            <div class="ls-video-play-btn" --ls-play-btn>
                                <div class="ls-play-btn-icn"></div>
                            </div>

                            <video id="video-live" controls="false" autoplay width="1200" height="675" src="https://www.radiogeneration.it/cdn/storage/file/tv.mp4"></video>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="radio-space">
                <div class="playback"></div>
            </div>

            <div class="container-box">
                <section class="news-section">
                    <feeder-view></feeder-view>
                </section>
                <!--
                <section class="news-section">
                    <h1>Nuovi DJ Mixsets</h1>
                </section>
                -->
            </div>
        </div>
        <div class="flex-grow"></div>
        <?php require_once __DIR__ . "/components/footer.php"; ?>
    </div>
    <style>
        .copyright-section {
            margin-left: 206px;
            font-size: 13px;
            color: #ffd9d9;
            font-weight: lighter;
            text-align: center;
        }
        .copy-text {
            
        }
        
        @media screen and (max-width: 840px) {
            .copyright-section {
                display: none;
            }
        }
    </style>
    <!-- <div class="radio-overlay" --radio-bar-overlay>
        <div class="radio-wrapper">
            <div class="flex align-center space-between">
                <div class="flex align-center">
                    <div class="radio-button" --play-button>
                        <div class="radio-play-circle">
                            <div class="play-button" --play-icon></div>
                        </div>
                    </div>
                    <div class="radio-text">
                        <div class="cta">
                            <span>Ascolta la diretta!</span>
                        </div>
                        <div class="slogan-mobile">
                            <span>The dance station</span>
                        </div>
                    </div>
                    <div class="copyright-section">
                        <div class="copy-text"><span>&copy; 2024 / 2025 www.radiogeneration.it</span></div>
                        <div class="copy-text"><span>Licenza SIAE: <?= $siaeLicence ?></span></div>
                    </div>
                </div>    
                <div class="radio-slogan-text">
                    <span>The dance station!</span>
                    <div class="slogan-small">
                        <span>Solo su www.radiogeneration.it</span>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <hidden-elements></hidden-elements>
<script>
        (() => {
            
            window.hls                   = new Hls();
            window.videoStreamingElement = document.getElementById('video-live');
            window.streamStatus          = "offline";
            window.isStreamingEnabled    = false;


            const videoPlayBtn  = document.querySelector("[--ls-play-btn]");
            const liveContainer = document.querySelector("[--live-container]");
            const lsOfflineText = document.querySelector("[--ls-offline-text]");

            videoPlayBtn.addEventListener("click", () => {
                if (window.videoStreamingElement.paused)
                    window.videoStreamingElement.play();
                else
                    window.videoStreamingElement.pause();
                
                // window.videoStreamingElement.currentTime = window.videoStreamingElement.duration;
            });


            window.videoStreamingElement.addEventListener("progress", () => {
                console.log("progress", 
                window.videoStreamingElement.buffered,
                window.videoStreamingElement.buffered.start(0),
                window.videoStreamingElement.buffered.end(0),
                    
                );

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

                    lsOfflineText.style.display = "none";
                    
                } else {
                    liveContainer.classList.add("offline");
                    window.hls.detachMedia(window.videoStreamingElement);
                    window.videoStreamingElement.muted    = true;                    
                    window.videoStreamingElement.src      = "https://www.radiogeneration.it/cdn/storage/file/tv.mp4";

                    videoPlayBtn.classList.remove("show-pb");

                    lsOfflineText.style.display = "block";  
                }
            };

           
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
            
            const app = new RadioGenApp();

            app.onPageSwitch("news-feeder", (state) => {
                if (state == "leaving") {
                    window.hls.detachMedia(window.videoStreamingElement);
                    liveContainer.style.display = "none";
                    window.isStreamingEnabled   = false;
                } else {
                    window.isStreamingEnabled = true;
                    liveContainer.style.display = "block";
                    window.onStreamStatusChange();
                }
            });
            
            try {
                app.route();
            } catch(ex) {
                // fallback in the worst case.
                document.body.querySelector('feeder-view').innerHTML = `<p style="padding-top:14px">Qualcosa è andato storto, <a style="cursor: pointer; text-decoration: underline" onclick="window.location.reload()">Ricarica</a></p>`;
            }

            setTimeout(() => {
                const e = document.querySelector('iframe[--radio-ltmr-frame]');
                e.remove();
            }, 3000);

            checkStreamingStatus();
        })();
        
        (() => {
          const sliderItems   = document.querySelector("[--slider-items]");
          const videoElements = sliderItems.querySelectorAll("video");
          for (const ve of videoElements)
              ve.play();
        })();
        
        (() => {
            // nice transition between page refreshes.
            window.addEventListener("load", () => {
                document.body.classList.remove("state-disappear");
            });
            window.addEventListener("beforeunload", () => {
                document.body.classList.add("state-disappear");
            });
            
        })();
    </script>
    
    <!-- Share link popup -->
        <script>
        (() => {
            const rgOverlay = document.getElementById('rg-sharePopup');
            const rgOpenBtn = document.querySelector('.rg-open-popup');
            const rgSnackbar = document.getElementById('rg-snackbar');
            
            rgOpenBtn.addEventListener('click', () => {
                rgOverlay.style.display = 'flex';
            });
            
            function rgClosePopup() {
                rgOverlay.style.display = 'none';
            }
            
            function rgCopyUrl() {
                const rgUrlInput = document.getElementById('rg-shareUrl');
                rgUrlInput.select();
                rgUrlInput.setSelectionRange(0, 99999); // For mobile
                document.execCommand("copy");
                
                // Close the popup
                rgClosePopup();
                
                // Show snackbar
                rgSnackbar.classList.add('rg-show');
                setTimeout(() => {
                rgSnackbar.classList.remove('rg-show');
                }, 3000); // Hide after 3 seconds
            }
            
            window.rgClosePopup = rgClosePopup;
            window.rgCopyUrl    = rgCopyUrl;
            
        })();
    </script>
</body>
</html>