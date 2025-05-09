<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/core.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/autoload.php';


use Database\PDOConnConfig as PDOConfig;
use Database\MySQLPDODatabase as MysqlDatabase;

$db = new MysqlDatabase(
    PDOConfig::create()
    ->host(DB_HOST)
    ->db(DB_DATABASE)
    ->user(DB_USER)
    ->pass(DB_PASS)
    ->charset(DB_CHARSET)
    ->options([
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ])
);

$videoFormats = [
    "mp4",
    "avi",
    "mpeg"
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
    
    <?php require_once __DIR__ . "/components/mobile-header.php"; ?>
    <?php require_once __DIR__ . "/components/header.php"; ?>

    <div class="content-wrapper content-shadow">
        
        <div class="main-content">
            <h1 style="margin-top:80px">Discografia!</h1>
        </div>
        <div class="flex-grow"></div>
        <div class="footer">
            <!--
            <div class="footer-big-section">
                <div class="footer-wrapper flex f-row space-between align-center">
                    <div class="footer-logo"><h2>Radio Generation</h2></div>
                    <div>
                        <div class="nav-item-list flex f-row space-even">
                            <div class="nav-item"><a href="/about-us">Chi siamo</a></div>
                            <div class="nav-item"><a href="/contacts">Contatti</></div>
                            <div class="nav-item"><a href="privacy-policy">Privacy Policy</a></div>                            
                        </div>
                    </div>
                    <div>A</div>    
                </div>
            </div>
            -->
            <!--<div class="brand-section">
                <span>&copy; www.radiogeneration.it, <span class="t-bold">tutti i diritti riservati</span></span>
            </div>
            -->
        </div>
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