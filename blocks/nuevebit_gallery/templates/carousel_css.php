<?php   
defined('C5_EXECUTE') or die("Access Denied."); 
$nav = Loader::helper('navigation');
$class = "active";

?>

<div class="carousel slide" id="carousel<?=$bID?>">
	
    <div class="carousel-inner">
    
            <?php  foreach($images as $imgInfo) { 
            $f = File::getByID($imgInfo['fID']);
			?>
            
			<div class="item <?=$class?>" style="background-image: url('<?=$f->getRelativePath(); ?>')">	
                <h2 class="phrase">
                    <?=$imgInfo["title"];?>
                </h2>
			</div>	
        
            <?php $class="";  } ?>

	</div>
    

</div>
