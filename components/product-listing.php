<?php 

require_once $_SERVER['DOCUMENT_ROOT'] . '/core.php';

$page_count     = 0;
$curr_page      = intval(@$_GET['p']);
$page_items     = [];

function feeds_count($type) {
    global $db;
    $conn        = $db->getConnection();
    $count = $conn->prepare("SELECT COUNT(*) as `count` FROM `feeds` WHERE `type` = ?");
    $count->bindParam(1, $type, PDO::PARAM_STR);
    
    if (!$count->execute())
        return 0;
    
    $count = $count->fetch(PDO::FETCH_ASSOC)['count'];
    return $count;
}

function fetch_feeds($page, $items_per_page, $type) {
    global $db;
    $conn        = $db->getConnection();
    $begin       = ($page - 1) * $items_per_page;

    $feeds = $conn->prepare("SELECT * FROM `feeds` WHERE `type` = ? ORDER BY `timestamp` DESC LIMIT $begin, $items_per_page");
    $feeds->bindParam(1, $type, PDO::PARAM_STR);

    if (!$feeds->execute())
        return [];

    return $feeds->fetchAll(PDO::FETCH_ASSOC);
}

if ($db->connect()) {
    $items_count = feeds_count($section);
    $page_count  = ceil($items_count / $items_per_page);

    if ($page_count > 0) {
        if ($curr_page < 1) $curr_page = 1;
        if ($curr_page > $page_count) $curr_page = $page_count;
                
        $page_items  = fetch_feeds($curr_page, $items_per_page, $section);
    }
}




?>

<!DOCTYPE html>
<html lang="it">
<?php $noFancyStuff = true; ?>
<?php require_once __DIR__ . "/head.php"; ?>

<body opacity-animation class="state-disappear" use-img-animation>    
    <?php require_once __DIR__ . "/mobile-header.php"; ?>
    <?php require_once __DIR__ . "/header.php"; ?>

    <div class="content-wrapper h-100 pd-81">
        <div class="content-section site-directory">
            <div class="fixed-content content-padded">
                <div class="section-headline-content">
                    <p class="site-dir"><a href="<?= SITE_URL; ?>/">DJ Sanny J</a> / <?= $page_name; ?></p>
                    <h1 class="section-headline"><?= $page_name; ?></h1>
                </div>
            </div>
        </div>
        

        <div class="content-section product-browsing">
            <div class="fixed-content content-padded">
                <div class="browse-items flex f-wrap">
                    <?php if (count($page_items) < 1) { ?>
                        <p style="font-size: 2rem; line-height: 25px; padding: 30px 0 0 0">Pagina vuota</p>
                    <?php } else { 
                    foreach ($page_items as $page_item) { ?>
                    <div class="browse-item" onclick="window.location.href='<?= SITE_URL . RELEASE_URI . "/" . $page_item["id"] ?>'">
                        <div class="browse-item-box"><img src="<?= $page_item['image_url']; ?>" alt="" /></div>
                        <div class="browse-details">
                            <div class="browse-title"><?= $page_item['title']; ?></div>
                            <div class="flex browse-category gap-2">
                                <div class="fa-icon headphone fs-small fw-lighter"></div>
                                <div class="fs-small fw-lighter"><?= date("Y", $page_item['timestamp']); ?></div>
                            </div>
                            <div class="flew-grow"></div>
                        </div>
                    </div>
                    <?php } ?>
                <?php } ?>
                </div>
                <?php
                    $display_range      = PAGINATION_RANGE;
                    
                    $prev_page          = $curr_page - 1;
                    $next_page          = $curr_page + 1;

                    if ($prev_page < 1) $prev_page = 1;
                    if ($next_page > $page_count) $next_page = $page_count;
                ?>
                <div class="navigation mobile-hide">
                    <a  <?= $curr_page <= 1 ? "" : "href=\"" . SITE_URL . $uri . "?p=" . $prev_page . "\"" ?> class="nav-next prev <?= $curr_page <= 1 ? "disabled" : "" ?>"><div class="fa-icon right left-rot"></div></a>
                    <?php if ($curr_page > 1) { ?>
                        <a href="<?= SITE_URL . $uri . "?p=1"; ?>" class="nav-number boundary">INIZIO</a>
                    <?php } ?>
                    <?php
                        $shift = 0;
                        for ($i = 0; $i < $display_range; ++$i) {
                            $pg_offset = $curr_page + $i - intdiv($display_range, 2);
                            if ($pg_offset < 1 && !$shift)
                                $shift += abs($pg_offset) + 1;
                    
                            $pg_offset += $shift;
                            if ($pg_offset > $page_count)
                                break;
                    ?>
                    <a href="<?= SITE_URL . $uri . "?p=" . $pg_offset; ?>" class="nav-number <?= $pg_offset == $curr_page ? "active" : ""; ?>"><?= $pg_offset; ?></a>
                    <?php
                        }
                    ?>
                    <?php if ($curr_page < $page_count) { ?>
                        <a href="<?= SITE_URL . $uri . "?p=" . $page_count; ?>" class="nav-number boundary">FINE</a>
                    <?php } ?>
                    
                    <a <?= $curr_page >= $page_count ? "" : "href=\"" . SITE_URL . $uri . "?p=" . $next_page . "\"" ?> class="nav-next next <?= $curr_page >= $page_count ? "disabled" : "" ?>"><div class="fa-icon right right-rot"></div></a>
                </div>

                <div class="mobile-appear">
                    <div class="flex f-row gap-4 align-stretch mobile-pagination">
                        <button onclick="changePage(this, <?= $prev_page; ?>)" class="flex-grow change-page-button <?= $curr_page <= 1 ? "disabled" : "" ?>">Precedente</button>
                        <select class="flex-grow change-page-select" onchange="changePage(this, this.value, true)">
                            <?php for ($i = 0; $i < $page_count; ++$i) { ?>
                            <option value="<?= $i + 1; ?>" <?= $i + 1 == $curr_page ? "selected" : "" ?>><?= $i + 1; ?></option>
                            <?php } ?>
                        </select>
                        <button onclick="changePage(this, <?= $next_page; ?>)" class="flex-grow change-page-button <?= $curr_page >= $page_count ? "disabled" : "" ?>">Successivo</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex-grow"></div>
        <?php require_once __DIR__ . "/footer.php"; ?>
    </div>
    <script>
        (() => {
          const app = new DJSannyJApp();
        })();
        
        const btns = document.body.querySelectorAll(".change-page-button");
        function changePage(target, page, disableButtons) {
            if (disableButtons)
                btns.forEach(btn => btn.classList.add("disabled"));
            else
                target.classList.add("disabled");

            window.location.href = "<?= SITE_URL . $uri . "?p=" ?>" + page;
        }

    </script>
</body>
</html>