<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/core.php'; ?>
<!DOCTYPE html>
<html lang="it">
<?php $noFancyStuff = true; ?>
<?php require_once __DIR__ . "/components/head.php"; ?>

<body opacity-animation class="state-disappear" use-img-animation>
    
    <?php require_once __DIR__ . "/components/mobile-header.php"; ?>
    <?php require_once __DIR__ . "/components/header.php"; ?>

    <div class="content-wrapper content-shadow">        
        <div class="main-content">
            <h1 style="margin-top:80px">Radioshow!</h1>
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