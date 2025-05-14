<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/core.php';

if (!$db->connect()) {
    header("Location: " . SITE_URL);
    exit;
}

$conn = $db->getConnection();
$aboutMeContent = $conn->prepare("SELECT * FROM `site_configuration`");
if (!$aboutMeContent->execute()) {
    header("Location: " . SITE_URL);
    exit;
}

$aboutMeContent = $aboutMeContent->fetch(PDO::FETCH_ASSOC)['about_me'];

?>

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
                    <p class="site-dir"><a href="<?= SITE_URL; ?>/">DJ Sanny J</a> / Bio</p>
                    <h1 class="section-headline">Bio</h1>
                </div>
            </div>
        </div>
        

        <div class="content-section product-browsing --lower-my-padding-top" style="padding: 60px 0;">
            <div class="fixed-content content-padded">
                <div class="release-about-subsection">
                    <?= $aboutMeContent; ?>
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
</body>
</html>