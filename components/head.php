<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <title><?= SITE_NAME; ?> - <?= routeName(); ?></title>
    
    <meta property="og:title" content="Radio Generation" />
    <meta property="og:description" content="The dance station!" />
    <meta property="og:image" content="https://www.radiogeneration.it/assets/social-media-image.jpg" />
    <meta property="og:url" content="https://www.radiogeneration.it" />
    
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-status-bar-style" content="white"/>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />
    <link rel="shortcut icon" href="<?= SITE_URL ?>/assets/djsannyj/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/style.css?cache=<?= cv(); ?>" />
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/about-us.css?<?= cv(); ?>" />
    
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/video-live-section.css?cache=<?= cv(); ?>" />
    
    <script type="text/javascript">
        ((m) => {
            m.rg_config = {
                DEBUG_MODE:                     <?= DEBUG_MODE ? 'true' : 'false'; ?>,
                FEEDER_ENDPOINT:                '<?= SITE_URL ?>/api/feeds',
                FEEDER_CREATE_SESSION_ENDPOINT: '<?= SITE_URL ?>/api/fcs',
                VIEW_FEED_ENDPOINT:             '<?= SITE_URL ?>/api/get_feed',
                RETRY_FEED_TIMEOUT: 5000, // 5sec
                MAX_RETRYING_ATTEMPS: 10,

                RADIO_STREAMING_URL: '<?= $radioLink; ?>',
                RADIO_FETCH_LINK_API: '<?= RADIO_FETCH_LINK_API; ?>',
                RADIO_OPENFIRE_LINK: '<?= OPEN_FIREWALL_LINK; ?>',
            };
        })(window);
    </script>
    
    <script src="<?= SITE_URL ?>/assets/js/dep-injector.js?v=<?= cv(); ?>"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

    <script src="<?= SITE_URL ?>/assets/js/date-formatter.js?<?= cv(); ?>"></script>
    
    <!-- Core Components -->
    <script src="<?= SITE_URL ?>/assets/js/core.js?v=<?= cv(); ?>"></script>
    <script src="<?= SITE_URL ?>/assets/js/audio-player.js?v=<?= cv(); ?>"></script>

    <!-- DOM Renderer -->
    <script src="<?= SITE_URL ?>/assets/js/dom-renderer.js?v=<?= cv(); ?>"></script>
    
    <!-- Feeder -->
    <script src="<?= SITE_URL ?>/assets/js/feeder.js?v=<?= cv(); ?>"></script>

    <!-- Static UX Handlers -->
    <!-- <script src="<?= SITE_URL; ?>/assets/js/radio-bar.js?v=<?= cv(); ?>"></script> -->
    <script src="<?= SITE_URL; ?>/assets/js/mobile-menu.js?v=<?= cv(); ?>"></script>

    <!-- Views Components -->
    <script src="<?= SITE_URL ?>/assets/js/views/components/row-stream-component.js?v=<?=cv();?>"></script>
    <script src="<?= SITE_URL ?>/assets/js/views/components/feed-cell.js?v=<?=cv();?>"></script>
    <script src="<?= SITE_URL ?>/assets/js/views/components/radio-player.js?v=<?=cv();?>"></script>

    <!-- Views -->
    <script src="<?= SITE_URL ?>/assets/js/views/feed-news-view.js?v=<?=cv();?>"></script>
    <script src="<?= SITE_URL ?>/assets/js/views/feed-podcast-view.js?v=<?=cv();?>"></script>
    <script src="<?= SITE_URL ?>/assets/js/views/feed-programs-view.js?v=<?=cv();?>"></script>
    <script src="<?= SITE_URL ?>/assets/js/views/about-us-view.js?v=<?=cv();?>"></script>
    <script src="<?= SITE_URL ?>/assets/js/views/view-post-view.js?v=<?=cv();?>"></script>
    <script src="<?= SITE_URL ?>/assets/js/views/privacy-policy-view.js?v=<?=cv();?>"></script>
    
    
    <!-- RadioGen App -->
    <script src="<?= SITE_URL ?>/assets/js/radiogen-app.js?v=<?= cv(); ?>"></script>

    <!-- 
    Standalone Image Slider
    <script defer src="<?= SITE_URL ?>/assets/js/image-slider.js?v=<?= cv(); ?>"></script>
     -->
    
    <?php if (!isset($noFancyStuff)) { ?>
    <script defer src="<?= SITE_URL ?>/assets/js/image-slider.js?v=<?= cv(); ?>"></script>
	<?php } ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inria+Sans:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="preload" href="<?= SITE_URL; ?>/assets/icons/pause.svg" as="image" />
    <link rel="preload" href="<?= SITE_URL; ?>/assets/icons/play-button.svg" as="image" />
    <link rel="preload" href="<?= SITE_URL; ?>/assets/icons/radio-loading-spinning.svg" as="image" />
    <link rel="preload" href="<?= SITE_URL; ?>/assets/pause-button.svg" as="image" />
    
    <link rel="stylesheet" href="assets/djsannyj/all.css"/>

    <style>
        .video-banner-viewer {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        [opacity-animation] {
            transition: opacity 200ms ease-in;
        }
        
        .state-disappear {
            opacity: 1;
        }
        
        [use-img-animation] img[animate] {
            transition: opacity 200ms ease-in-out;
            opacity: 0;
        }
        
        [use-img-animation] img[animate].--img-loaded {
            opacity: 1;
        }
        
        .video-element:hover .video-live-controls {
            opacity: 1;
        }
        .video-element:hover .video-live-controls .video-live-btn {
            opacity: 1;
            transform: translateY(0);
        }
        
        .video-live-controls {
            position: absolute;
            z-index: 999;
            margin: 20px;
            background: #2e2e2e;
            box-shadow: 0 0 30px 14px rgba(0, 0, 0, 0.3);
            border-radius: 9px;
            padding-top: 6px;
            padding-bottom: 3px;
            padding-left: 10px;
            padding-right: 10px;
            opacity: 0;
            transition: opacity 200ms ease-in-out;
        }
        
        .video-live-controls .video-live-btn {
            opacity: 0;
            transform: translateY(10px);
            transition: transform 200ms ease-in-out, opacity 200ms ease-in-out;
        }
        
        .video-live-controls .video-live-btn button {
            width: 33px;
            height: 32px;
            background: transparent;
            border: none;
            border-radius: 50%;
            cursor: pointer;
        }
        
        .video-live-controls .video-live-btn button:hover {
            background-color: #4b4b4b!important;
        }
        
        .video-live-controls .video-live-btn button:active {
            background-color: #6e6e6e!important;
        }
        
        .lvbutton-img {
            width: 100%;
            height: 100%;
            filter: invert(1);
            background-image: url("<?= SITE_URL ?>/assets/link.png");
            background-size: 60%;
            background-repeat: no-repeat;
            background-position: center;
        }
    </style>
    <!-- <div class="video-live-controls"><div class="video-live-btn"><button>Hello</button></div></div> -->
    
    <style>
        
    .rg-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 9999999999;
      padding: 1rem;
      --rg-light-gray: #ddd;
      --rg-dark-gray: #333;
    }

    .rg-popup {
        background: #121212;
        padding: 2rem;
        border-radius: 1rem;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        text-align: center;
        animation: rg-fadeIn 0.3s ease;
    }

    .rg-popup h2 {
      margin-bottom: 1rem;
      font-size: 1.5rem;
      color: #fff;
    }

    .rg-popup input {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid #262626;
        border-radius: 0.5rem;
        font-size: 1rem;
        margin-bottom: 1.5rem;
        background: #1c1c1c;
        outline: none;
        color: #fff;
    
    }

    .rg-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      justify-content: center;
    }

    .rg-buttons button {
      flex: 1 1 45%;
      min-width: 120px;
      padding: 0.7rem 1rem;
      border: none;
      border-radius: 0.5rem;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
    }

    .rg-buttons button.rg-copy {
      background: #007BFF;
      color: white;
    }

    .rg-buttons button.rg-copy:hover {
      background: #006cdf;
    }

    .rg-buttons button.rg-close {
      background: var(--rg-light-gray);
      color: var(--rg-dark-gray);
    }

    .rg-buttons button.rg-close:hover {
      background: #ccc;
    }

    /* Snackbar Notification */
    .rg-snackbar {
      visibility: hidden;
      min-width: 250px;
      background-color: #333;
      color: #fff;
      text-align: center;
      border-radius: 8px;
      padding: 1rem;
      position: fixed;
      z-index: 9999999999;
      left: 1rem;
      bottom: 1rem;
      font-size: 1rem;
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .rg-snackbar.rg-show {
      visibility: visible;
      opacity: 1;
      transform: translateY(0);
    }

    /* Mobile position */
    @media (max-width: 600px) {
      .rg-snackbar {
        left: 50%;
        bottom: auto;
        top: 1rem;
        transform: translateX(-50%) translateY(-20px);
      }

      .rg-snackbar.rg-show {
        transform: translateX(-50%) translateY(0);
      }
    }

    @keyframes rg-fadeIn {
      from { opacity: 0; transform: scale(0.95); }
      to { opacity: 1; transform: scale(1); }
    }

    /* Responsive Text Sizes */
    @media (max-width: 400px) {
      .rg-popup h2 {
        font-size: 1.3rem;
      }
      .rg-popup input {
        font-size: 0.9rem;
      }
      .rg-buttons button {
        font-size: 0.9rem;
        padding: 0.6rem 0.8rem;
      }
    }
    </style>
</head>