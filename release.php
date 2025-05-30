<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/core.php';

$content_id    = @intval(@$_GET['item']);

function getMimeFromUrlExtension($url) {
    $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

    $mimeTypes = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'svg'  => 'image/svg+xml',
        'bmp'  => 'image/bmp',
        'ico'  => 'image/vnd.microsoft.icon',
        'tif'  => 'image/tiff',
        'tiff' => 'image/tiff',
    ];

    return $mimeTypes[$ext] ?? 'application/octet-stream';
}

if (!$db->connect()) {
    header("Location: " . SITE_URL);
    exit;
}

$conn = $db->getConnection();
$item = $conn->prepare("SELECT * FROM `feeds` WHERE `id` = ?");
$item->bindParam(1, $content_id, PDO::PARAM_INT);

if (!$item->execute()) {
    header("Location: " . SITE_URL);
    exit;
}

$item = $item->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    header("Location: " . SITE_URL);
    exit;
}

$item_title     = $item['title'];
$content_image  = $item['image_url'];
$video_file     = $item['video_file_url'];
$youtube_url    = $item['video_urls'];

$audio_cover      = $item['audio_cover_url'];
$audio_preview    = $item['audio_url'];

$audio_key         = null;
$audio_format      = null;

$audio_preview_key = @explode('/', $audio_preview);
$audio_preview_key = $audio_preview_key[count($audio_preview_key) - 1];
$audio_preview_key = @explode('.', $audio_preview_key);
$audio_key         = @$audio_preview_key[0];
$audio_format      = @$audio_preview_key[1];


$audio_title      = $item['audio_title'];

$content_desc   = $item['text_content'];
$timestamp      = $item['timestamp'];
$post_year      = $item['post_year'];

$tracklistToken = $item['tracklist_token'];

$tracklists    = [];
$mime          = getMimeFromUrlExtension($content_image);

// if (!empty($item['audio_url'])) {
//     $tracklists[] = [
//         "id"          => $item['id'],
//         "title"       => $item['title'],
//         "url"         => $item['audio_url'],
//         "audio_cover" => $item['audio_cover_url']
//     ];
// }

$tracklistRecords = $conn->prepare("SELECT * FROM `tracklist_editor` WHERE `token` = ?");
$tracklistRecords->bindParam(1, $tracklistToken, PDO::PARAM_INT);

if ($tracklistRecords->execute()) {
    $tracklistRecords = $tracklistRecords->fetchAll(PDO::FETCH_ASSOC);
    foreach ($tracklistRecords as $track) {
        $tracklists[] = [
            "id"          => $track['id'],
            "title"       => $track['title'],
            "url"         => $track['audio_url'],
            "duration"    => $track['duration'],
            "audio_cover" => ""
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="it">
<?php $noFancyStuff = true; $noHeadClose = true; $excludeOGs = true; ?>
<?php require_once __DIR__ . "/components/head.php"; ?>
<meta name="robots" content="follow, index, max-snippet:-1, max-video-preview:-1, max-image-preview:large"/>
<link rel="canonical" href="<?= SITE_URL . $uri; ?>" />
<meta property="og:locale" content="it_IT" />
<meta property="og:type" content="article" />
<meta property="og:title" content="<?= $item_title; ?>" />
<meta property="og:url" content="<?= SITE_URL . $uri; ?>" />
<meta property="og:site_name" content="<?= SITE_NAME; ?>" />
<meta property="og:image" content="<?= $content_image; ?>" />
<meta property="og:image:secure_url" content="<?= $content_image; ?>" />
<meta property="og:image:width" content="640" />
<meta property="og:image:height" content="640" />
<meta property="og:image:alt" content="<?= htmlentities($item_title); ?>" />
<meta property="og:image:type" content="<?= $mime; ?>" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?= htmlentities($item_title); ?>" />
<meta name="twitter:image" content="<?= $content_image; ?>" />
<style>
.custom-tb-info h2 {
    font-size: 1.2rem;
    font-weight: 400;
    margin-bottom: unset;
    text-align: left;
}

.custom-tb-info tr td:first-of-type {
    padding-left: 20px;
}

.custom-tb-info a {
    color: #6872cb;
}

.custom-tb-info a:hover {
    text-decoration: underline;
}
</style>
<style>
.dw-audio-player {
    display: flex;
    align-items: center;
    background: white;
    border-radius: 12px;
    margin-top: 43px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    max-width: 600px;
    width: 100%;
}

.dw-audio-cover {
    width: 100px;
    height: 100px;
    object-fit: cover;
    margin-right: 20px;
}

.dw-audio-controls {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 16px;
    flex: 1;
}

.dw-audio-buttons {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 11px;
}

.dw-audio-play-btn,
.dw-audio-download-btn {
    font-size: 24px;
    cursor: pointer;
    color: #333;
}

.dw-audio-play-btn:hover,
.dw-audio-download-btn:hover {
    color: #007bff;
}

.dw-audio-timeline {
    margin-top: 10px;
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 6px;
    background: #e0e0e0;
    border-radius: 5px;
    outline: none;
}

.dw-audio-timeline::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
    cursor: pointer;
    margin-top: -3px;
}
</style>
<style>
table h2 {
    font-size: 1em;
    margin-bottom: 0;
}

table a {
    color: #506dce;
}

table td {
    padding: 0px 0 0 6px;
}
</style>
</head>

<body opacity-animation class="state-disappear" use-img-animation>    
    <?php require_once __DIR__ . "/components/mobile-header.php"; ?>
    <?php require_once __DIR__ . "/components/header.php"; ?>

    <div class="content-wrapper h-100 pd-81">
        <div class="content-section site-directory">
            <div class="fixed-content content-padded">
                <div class="section-headline-content">
                    <p class="site-dir"><a href="<?= SITE_URL; ?>/">DJ Sanny J</a> / <?= $item_title; ?></p>
                    <h1 class="section-headline"><?= $item_title; ?></h1>
                </div>
            </div>
        </div>
        
        <div class="content-section product-browsing --lower-my-padding-top" style="padding: 60px 0;">
            <div class="fixed-content content-padded">
                <div class="flex col-on-mobile" style="gap: 55px">
                    <div class="w-50 padded-box flex-shrink-0">
                        <div class="pt-50 relative">
                            <img class="release-img" src="<?= $content_image; ?>" alt="">
                        </div>
                        <div class="release-desc-section" style="padding-top: 35px; padding-bottom:10px">
                            <h2 class="subsection-headline">Share</h2>
                            <div class="share-subsection">
                                <div class="flex f-row gap-4">
                                    <a href="#" onclick="window.open('http://www.facebook.com/sharer.php?u=<?= SITE_URL . $uri; ?>', 'sharer', 'toolbar=0,status=0,width=300,height=300');" class="social-link is-link facebook hover-black share-social-link"></a>
                                    <a href="#" onclick="window.open('https://twitter.com/intent/tweet?text=<?= SITE_URL . $uri; ?>', 'sharer', 'toolbar=0,status=0,width=300,height=300');" class="social-link is-link twitter hover-black share-social-link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow padded-box" style="min-width: 0">
                        <?php if (count($tracklists) > 0) { ?>
                        <div class="release-desc-section">
                            <h2 class="subsection-headline">Tracklist</h2>
                            <div class="tracklist-subsection">
                                <?php
                                foreach ($tracklists as $index => $track) {
                                    $audioId = "audio_" . $track['id'];
                                    $btnId = "btn_" . $track['id'];
                                    $durId = "dur_" . $track['id'];
                                    ?>
                                <div class="djsn-track-row">
                                    <div>#<?= $index + 1 ?></div>
                                    <div class="djsn-track-title"><?= $track['title'] ?></div>
                                    <div id="<?= $durId ?>" class="djsn-track-duration"><?= $track['duration'] ?></div>
                                    <?php if (!empty($track['url'])) { ?>
                                    <i id="<?= $btnId ?>" class="--fa-solid play djsn-play-button" data-audio="<?= $audioId; ?>"></i>
                                    <audio id="<?= $audioId ?>" src="<?= $track['url'] ?>"></audio>
                                    <?php } ?>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if (!empty($audio_preview)) { ?>
                        <div class="release-desc-section">
                            <h2 class="subsection-headline">Audio</h2>
                            <div class="dw-audio-player">
                            <img class="dw-audio-cover" src="<?= $audio_cover; ?>" alt="Cover" />
                            <div class="dw-audio-controls">
                                <div class="dw-audio-buttons">
                                <i class="fa-solid fa-play dw-audio-play-btn"></i>
                                <a class="dw-audio-download-btn" href="<?= CDN_ENDPOINT . 'download-audio?title='. urlencode($audio_title) .'&k=' . $audio_key . '.' . $audio_format; ?>" download>
                                    <i class="fa-solid fa-download"></i>
                                </a>
                                </div>
                                <input type="range" class="dw-audio-timeline" value="0" />
                            </div>
                            <audio class="download-audio" src="<?= $audio_preview; ?>"></audio>
                            </div>
                            <script>
                                (() => {
                                    const audio     = document.querySelector("audio.download-audio");
                                    const playBtn   = document.querySelector(".dw-audio-play-btn");
                                    const timeline  = document.querySelector(".dw-audio-timeline");

                                    playBtn.addEventListener("click", () => {
                                        if (audio.paused) {
                                            audio.play();
                                            playBtn.classList.replace("fa-play", "fa-pause");
                                        } else {
                                            audio.pause();
                                            playBtn.classList.replace("fa-pause", "fa-play");
                                        }
                                    });

                                    audio.addEventListener("timeupdate", () => {
                                        timeline.value = (audio.currentTime / audio.duration) * 100 || 0;
                                    });

                                    timeline.addEventListener("input", () => {
                                        audio.currentTime = (timeline.value / 100) * audio.duration;
                                    });
                                })();
                            </script>
                        </div>

                        <?php } ?>
                        <div class="release-desc-section">
                            <h2 class="subsection-headline">About</h2>
                            <?php if (!empty($video_file)) { ?>
                            <div class="video-media">
                                <video class="video-viewer" src="<?= $video_file; ?>" controls="" autoplay="" width="640" height="360"></video>
                            </div>
                            <?php } ?>
                            <?php if (!empty($youtube_url)) { ?>
                            <div class="video-media">
                                <div class="yt-iframe">
                                    <iframe style="width: 100%; height: 100%" src="<?= $youtube_url; ?>?autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen=""></iframe>
                                </div>
                            </div>
                            <?php } ?>
                            
                            <div class="release-about-subsection">
                                <?= $content_desc; ?>
                            </div>
                            <div class="release-about-subsection">
                                <?php
                                if ($post_year) {
                                ?>
                                <div>
                                    <span style="color: var(--primary-dark);">Anno di rilascio:</span>
                                    <span style="color: #5a6370;"><?= $post_year; ?></span>
                                </div>  
                                <?php } ?>
                                <div>
                                    <span style="color: var(--primary-dark);">Data del post:</span>
                                    <span style="color: #5a6370;"><?= date('d-m-Y', $timestamp); ?></span>
                                </div>                        
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex-grow"></div>
        <?php require_once __DIR__ . "/components/footer.php"; ?>
    </div>
    <script>
        (() => {
          const app = new DJSannyJApp();
        })();
    </script>
    <script>
    
    const buttons = document.querySelectorAll('.djsn-play-button');
    let currentlyPlaying = null;

    buttons.forEach(button => {
        const audioId = button.dataset.audio;
        const audio = document.getElementById(audioId);
        
        if (!audio) return;

        const durElem = document.getElementById('dur_' + audioId.split('_')[1]);

        // Load duration
        audio.addEventListener('loadedmetadata', () => {
            const min = Math.floor(audio.duration / 60);
            const sec = Math.floor(audio.duration % 60).toString().padStart(2, '0');
            durElem.textContent = `${min}:${sec}`;
        });

        button.addEventListener('click', () => {
            if (currentlyPlaying && currentlyPlaying !== audio) {
                currentlyPlaying.pause();
                currentlyPlaying.currentTime = 0;
                const prevBtn = document.querySelector(`.djsn-play-button[data-audio="${currentlyPlaying.id}"]`);
                if (prevBtn) prevBtn.classList.replace('pause', 'play');
            }

            if (audio.paused) {
                audio.play();
                button.classList.replace('play', 'pause');
                currentlyPlaying = audio;
            } else {
                audio.pause();
                button.classList.replace('pause', 'play');
                currentlyPlaying = null;
            }
        });

        audio.addEventListener('ended', () => {
            button.classList.replace('pause', 'play');
            currentlyPlaying = null;
        });
    });
    </script>

</body>
</html>