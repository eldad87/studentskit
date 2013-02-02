<script type="text/javascript">
    function rate() {

    }
    /**
     * Need to have:
     *  data-user_lesson_id
     *      used as: {X}_ + user_lesson_id
     *          X: container - should contain the whole review div, it will be removed on success
     *          X: rating - the div that contains the starts
     *          X: rating_error - the div with the rating error
     *          X: review - the div that contains the text review
     *          X: review_error - the div with the text review error
     *
     * @param buttonSelector
     */
    rate.prototype.bindSendButtons = function(buttonSelector) {
        var rateObj = this;

        //Remove old events
        $(buttonSelector).unbind();

        $(buttonSelector).click(function(e) {
            e.preventDefault();

            /* 1. Get values */
            //Get user_lesson_id
            var userLessonId = $(this).data('user_lesson_id');
            //Get rating
            var rating = rateObj.getRating(userLessonId);
            //Get review
            var review = rateObj.getReview(userLessonId);

            /* 2. Submit rating data */
            $.ajax({
                url: jQuery.nano('<?php echo Router::url(array('controller'=>'Student', 'action'=>'setReview', '{user_lesson_id}')); ?>', {user_lesson_id: userLessonId}),
                type: 'post',
                data: {userLessonId: userLessonId, rating: rating, review: review},
                dataType: 'json'

            }).done(function ( data ) {

                    if(data['response']['title'][0]=='Error') {


                        if(data['response']['results'] && data['response']['results']['validation_errors']!=undefined) {
                            var validationErrors = data['response']['results']['validation_errors'];

                            //Set review errors
                            if(validationErrors['comment_by_student']!=undefined) {
                                $('#review_error_' + userLessonId).html(validationErrors['comment_by_student']);
                            } else if(validationErrors['comment_by_teacher']!=undefined) {
                                $('#review_error_' + userLessonId).html(validationErrors['comment_by_teacher']);

                                //Clear errors
                            } else {
                                $('#rating_error_' + userLessonId).html(' ');
                            }

                            //Set rating errors
                            if(validationErrors['rating_by_student']!=undefined) {
                                $('#rating_error_' + userLessonId).html(validationErrors['rating_by_student']);
                            } else if(validationErrors['rating_by_teacher']!=undefined) {
                                $('#rating_error_' + userLessonId).html(validationErrors['rating_by_teacher']);

                                //Clear errors
                            } else {
                                $('#rating_error_' + userLessonId).html(' ');
                            }
                        }

                        $('#error_' + userLessonId).html(data['response']['description'][0]);


                        //Success
                    } else {
                        $('#container_' + userLessonId).hide();
                    }
                });

        });
    }

    //Get the amount of marked starts, return false if none
    rate.prototype.getRating = function(userLessonId) {
        return $('#rating_' + userLessonId + ' .star-active1').length;
    }

    //Return the user review, return false if its null
    rate.prototype.getReview = function(userLessonId) {
        return $('#review_' + userLessonId).val();
    }


    $(document).ready(function(){
        initMenuLinks();

        var rateObj = new rate();
        rateObj.bindSendButtons('.rateButton');
    });




</script>
<?php
$otherUserType = ($userType=='student' ? 'teacher' : 'student');
?>
<div class="cont-span15 cbox-space">
    <div class="fullwidth pull-left">
        <h2 class="space2"><strong>User Rating</strong></h2>
        <div class="fullwidth pull-left space12">

            <p class="pull-left clear-left fontsize1"><?php echo 'Here you can find all the lessons that pending for your rating'?><br /><br /></p>

            <p class="fontsize1 pull-left clear-left">
                <strong>Your Average Rate :</strong>
            </p>
            <a href="#" class="pull-left load2" rel="<?php echo Router::url(array('controller'=>ucfirst($userType),
                                                                                    'action'=>($allowRate ? 'myReviews' : 'awaitingReview'))); ?>"><?php
                echo $this->Layout->ratingNew($avarageRating, false, '');
                ?></a>



        </div>
        <div class="form-first">
            <form class="sk-form">
                <fieldset>
                    <div class="upr-heading-form radius3">
                        <p class="fontsize1 first-tex-in-box"><?php echo __('Description'); ?></p>
                        <p class="fontsize1 first-tex-in-box2"><?php echo __('Rating'); ?></p>
                        <p class="fontsize1 first-tex-in-box3"><?php echo __('Comments'); ?></p>
                        <?php
                        if($allowRate) {
                            echo '<p class="fontsize1 first-tex-in-box4">',__('Send'),'</p>';
                        }
                        ?>

                    </div>
                    <ul class="form-teach">

                        <?php
                        foreach($reviews AS $review) {
                            $lessonURL = array('controller'=>'Lessons',
                                                'action'=>($review['UserLesson']['lesson_type']==LESSON_TYPE_LIVE ? 'index' : 'video'),
                                                $review['UserLesson']['user_lesson_id']);

                            ?>

                            <li id="container_<?php echo $review['UserLesson']['user_lesson_id']; ?>">
                                <div class="fullwidth pull-left space12">
                                    <table class="fullwidth">
                                        <tr>

                                            <td class="tech-first-box error" id="error_<?php echo $review['UserLesson']['user_lesson_id']; ?>"></td>
                                            <td class="tech-sec-box" id="rating_error_<?php echo $review['UserLesson']['user_lesson_id']; ?>"></td>
                                            <td class="tech-thir-box" id="review_error_<?php echo $review['UserLesson']['user_lesson_id']; ?>"></td>
                                            <?php
                                            if($allowRate) {
                                                echo '<td class="tech-fourth-box"></td>';
                                            }
                                            ?>
                                        </tr>

                                        <tr>

                                            <td class="tech-first-box">

                                                <div class="fullwidth">
                                                    <div class="tech-pic">
                                                        <?php
                                                        echo $this->Html->image($this->Layout->image($review['UserLesson']['image_source'], 60, 60),
                                                            array('alt' => 'Lesson image', 'url'=>$lessonURL)); ?>
                                                    </div>
                                                    <div class="teach-space-left">
                                                        <h6><strong class="a-black"><?php echo $this->Html->link($review['UserLesson']['name'], $lessonURL); ?></strong></h6>
                                                        <p><?php echo $this->TimeTZ->niceShort($review['UserLesson']['datetime']); ?></p>
                                                        <strong><?php echo $this->Html->link($review[ ucfirst($otherUserType) ]['username'], array('controller'=>'Home', 'action'=>$otherUserType, $review['UserLesson'][$otherUserType.'_user_id'])); ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="tech-sec-box">
                                                <div class="fullwidth">
                                                    <?php echo $this->Layout->ratingNew( ($allowRate ? null : $review['UserLesson']['rating_by_'.$otherUserType]), $allowRate, 'pull-left pad8 centered', 'rating_'.$review['UserLesson']['user_lesson_id']); ?>
                                                </div>
                                            </td>
                                            <td class="tech-thir-box">
                                                <div class="fullwidth">
                                                    <div class="fullwidth clear-left float-left">
                                                        <?php
                                                        echo '<textarea class="x-large" rows="3" '.($allowRate ? '' : 'readonly').' id="review_'.$review['UserLesson']['user_lesson_id'].'">'.($allowRate ? null : $review['UserLesson']['comment_by_'.$otherUserType]).'</textarea>';
                                                        ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <?php
                                            if($allowRate) {
                                                ?>
                                                <td class="tech-fourth-box">
                                                    <div class="fullwidth">
                                                        <input type="button" class="btn btn-success space37 rateButton" data-user_lesson_id="<?php echo $review['UserLesson']['user_lesson_id']; ?>" value="Rate" />
                                                    </div>
                                                </td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                    </table>
                                </div>
                            </li>
                            <?php
                        }

                        if(!$reviews) {
                            echo '<p>',__('No lessons waiting for your review yet.'),'</p>';
                        }
                        ?>

                    </ul>
                </fieldset>
            </form>
        </div> <!-- /form-first -->
    </div> <!-- /fullwidth -->
</div>