<!-- Searchbar
        ================================================== -->
<script>
$(document).ready(function(){

    $('#search_terms').blur(function() {
        if($(this).val() == '') {
            $(this).val('Enter your topic, I.E. Linear Algebra');
        }
    });
    $('#search_terms').focus(function() {
        if($(this).val() == 'Enter your topic, I.E. Linear Algebra') {
            $(this).val('');
        }
    });

    if($('#search_terms').val() == '') {
        $('#search_terms').blur();
    }
});
</script>

<Section class="searchbar">
    <div class="searchbar-inner">
        <h2>Search Here</h2>
        <div class="searchbox-bg">
            <?php echo $this->Form->create(false, array('url'=>array('controller'=>(isSet($controller) ? $controller : 'Home'), 'action'=>'searchSubject'), 'id'=>'search_form', 'type'=>'get')); ?>
            <div class="search-fullwidth">
                <div class="search-right-btn-left">
                    <div class="search-box-main">

                        <label></label>
                        <?php	echo $this->Form->input('search_terms', array('label'=>false)); ?>

                    </div>
                </div>
                <div class="search-right-btn">
                    <div class="btn-group">
                        <button type="button" class="btn-selt-catg dropdown-toggle" data-toggle="dropdown"><i class="iconSmall-info btn-selt-catg-info"></i>Select A Category <span class="caret btn-selt-catg-arw"></span></button>
                        <ul class="dropdown-menu">
                            <li><a href="#">Online Lessons </a></li>
                            <li><a href="#">Face to face Lessons </a></li>
                            <li><a href="#">1 on 1 Lessons</a></li>
                            <li><a href="#">Group Teaching </a></li>
                            <li><a href="#">1 time Lessons </a></li>
                            <li><a href="#">Course </a></li>
                        </ul>
                    </div><!-- /btn-group -->
                    <button type="submit" class="btn-search">SEARCH</button>
                </div>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
        <small>*Try your fav. subjects</small>
    </div>
</Section>