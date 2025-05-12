<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/core.php'; ?>

<!DOCTYPE html>
<html lang="it">
<?php $noFancyStuff = true; ?>
<?php require_once __DIR__ . "/components/head.php"; ?>

<body opacity-animation class="state-disappear" use-img-animation>    
    <?php require_once __DIR__ . "/components/mobile-header.php"; ?>
    <?php require_once __DIR__ . "/components/header.php"; ?>

    <div class="content-wrapper h-100 pd-81">
        <div class="content-section site-directory">
            <div class="fixed-content content-padded">
                <div class="section-headline-content">
                    <p class="site-dir"><a href="<?= SITE_URL; ?>/">DJ Sanny J</a> / Discografia</p>
                    <h1 class="section-headline">Discografia</h1>
                </div>
            </div>
        </div>

        <div class="content-section product-browsing">
            <div class="fixed-content content-padded">
                <h1 class="section-headline">BROWSE</h1>
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
</body>
</html>