<?php   
defined('C5_EXECUTE') or die("Access Denied."); 

$nh = Loader::helper("navigation");
$pageUrl = $nh->getCollectionURL(Page::getCurrentPage());
$pagerUrl = $pageUrl . "?page=";
?>

<div id="nb_gallery_<?=$this->bID?>" class="nb_gallery">
    <div class="sets">
        <ul class="gente">
            <?php 
            foreach ($selectedFileSets as $set): 
                if ($set["showThumbnail"] == 0):
            ?>
                <li><a class="mes" data-fsId="<?=$set["fsName"]?>" href=""><?=$set["fsName"]?></a></li>
            <?php else: 
                $thumbnail = File::getByID($thumbnails[intval($set["id"])])->getRelativePath();
            ?>
                <li><a data-fsId="<?=$set["fsName"]?>" href=""><span><?=$set["fsName"]?></span><img src="<?=$thumbnail?>" alt="" /></a></li>
            <?php endif; endforeach; ?>
        </ul>

        <div class="pagination">
            <?php if ($totalSetsCount > $maxSetsCount): ?>
            <ul>
                <?php if ($currentPage == 0):?>
                <li class="arrow">Prev</li>
                <?php else: ?>
                <li class="arrow"><a href="<?=$pagerUrl . ($currentPage - 1)?>">Prev</a></li>
                <?php endif; ?>
                
                <?php 
                $pages = $totalSetsCount / $maxSetsCount;
                for ($i = 0; $i < $pages; $i++):
                    if ($currentPage == $i):
                ?>
                    <li class="active"><?=$i+1?></li>
                <?php else: ?>
                    <li><a href="<?=$pagerUrl . $i ?>"><?=$i+1?></a></li>
                <?php endif; endfor; ?>
                    
                <?php if ($currentPage == $pages - 1): ?>
                <li class="arrow">Sig</li>
                <?php else: ?>
                <li class="arrow"><a href="<?=$pagerUrl . ($currentPage + 1)?>">Sig</a></li>
                <?php endif; ?>
            </ul>
            <?php endif; ?>

            <form method="get" action="">
                <select name="year">
                    <?php 
                    foreach ($availableYears as $year): 
                        $selected = ($year == $selectedYear) ? "selected='selected'" : ""; 
                    ?>
                    <option <?=$selected?> value="<?=$year?>"><?=$year?></option>
                    <?php endforeach; ?>
                </select>
                
                <select name="month">
                    <option value="0">mes</option>
                    <?php 
                    foreach ($availableMonths as $id => $month): 
                        $selected = ($month == $selectedMonth) ? "selected='selected'" : "";
                    ?>
                    <option <?=$selected?> value="<?=$month?>"><?=$month?></option>
                    <?php endforeach; ?>
                </select>

                <input type="submit" value="Submit" />
            </form>
        </div>
    </div>

    <div class="galleria" id="galleria<?=$bID?>">
        <!--
        Content will be loaded through javascript
        TODO: Usar <noscript/> para usuarios que no tengan javascript habilitado
        -->
    </div>
</div>

