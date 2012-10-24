<div class="categorybox radius3">
    <h3 class="radius1"><strong>Category</strong></h3>
    <p class="category-subheadbar">Category</p>
    <ul class="categorystripbox">
        <?php
        foreach($facet_categories AS $subjectCategory) {
            echo
            '<li>
                <span>(',$subjectCategory['count'],')</span>
                ',$this->Html->link($subjectCategory['name'], array('?'=>am($this->params['url'], array('category_id'=>$subjectCategory['subject_category_id']))), array('class'=>'categorystiptext')),'
            </li>';
        }
        ?>
    <p class="category-subheadbar">Lesson Type</p>
    <ul class="categorystripbox categorystripbox1">
        <li>
            <div class="categorystiptext">
                <input type="checkbox" name="live"><label>Live</label>
            </div>
            <!--<span>()</span>-->
        </li>
        <li>
            <div class="categorystiptext">
                <input type="checkbox" name="video"><label>Video</label>
            </div>
            <!--<span>()</span>-->
        </li>
    </ul>

    <p class="category-subheadbar pricesubheader">Price</p>
    <ul class="categorystripbox categorystripbox1">
        <li class="nobackground">
            <input type="text" value="" />
            <label id="middletexr"> to</label>
            <input type="text" value="" />
            <label id="middletexr1"> $</label>
            <!--<input type="text" value="$" >-->
        </li>
    </ul>

    <p class="category-subheadbar pricesubheader">Rate</p>
    <ul class="categorystripbox categorystripbox1">
        <li class="nobackground">
            <input type="text" value="" >
            <label id="middletexr1"> to</label>
            <input type="text" value="" >
        </li>
    </ul>
</div>