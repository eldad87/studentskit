(function( $ ){

    var methods = {
        _renderQuestionBlock: function(questionId) {

            var settings = $(this).data('quizBuilder');

            //Clone template
            var questionBlock = settings.templates.questionBlock.clone(true);

            //Replace IDs
            <!--Question block-->
            questionBlock.attr('id', 'question_block_' + questionId);

            <!--Select right answer-->
            questionBlock.find('#right_answer_block_question_id label').attr('for', 'right_answer_select_' + questionId);
            questionBlock.find('#right_answer_select_question_id').attr('id', 'right_answer_select_' + questionId);
            questionBlock.find('#right_answer_block_question_id').attr('id', 'right_answer_block_' + questionId);

            <!--Question-->
            questionBlock.find('#question_question_id label').append( (questionId+1) + ' :');
            questionBlock.find('#question_question_id label').attr('for', 'question_text_' + questionId);
            questionBlock.find('#question_text_question_id').attr('id', 'question_text_' + questionId);
            questionBlock.find('#delete_question_question_id').attr('id', 'delete_question_' + questionId);
            questionBlock.find('#question_question_id').attr('id', 'question_' + questionId);

            <!--Answers-->
            questionBlock.find('#answer_list_question_id').attr('id', 'answer_list_' + questionId);
            questionBlock.find('#answer_block_answer_id').remove(); //No need for answers.


            //Append new question to view
            questionBlock.appendTo(settings.templates.questionList);



            //Append question text and answers
            var questionData = settings.data[questionId];
            if(questionData) {
                //Add question text
                $('#question_text_' + questionId).val(questionData.q);

                //Add answers
                var $self = $(this);
                $.each(questionData.a, function(index, value) {
                    $self.quizBuilder('_renderAnswerBlock', questionId, index);
                });
            }
        },

        _renderAnswerBlock: function(questionId, answerId) {

            var settings = $(this).data('quizBuilder');

            //Clone template
            var answerBlock = settings.templates.answerBlock.clone(true);

            //Replace IDs
            <!--Answers-->
            answerBlock.find('label').append( (answerId+1) + ' :');
            answerBlock.find('label').attr('for', 'answer_text_' + questionId + '_' + answerId);
            answerBlock.find('#answer_text_question_id_answer_id').attr('id', 'answer_text_' + questionId + '_' + answerId);
            answerBlock.find('#delete_answer_question_id_answer_id').attr('id', 'delete_answer_' + questionId + '_' + answerId);
            answerBlock.attr('id', 'answer_block_' + questionId + '_' + answerId);


            //Append new question to view
            answerBlock.appendTo(
                $('#question_block_' + questionId + ' .answer-list')
            );

            //Append text to answer
            $('#answer_text_' + questionId + '_' + answerId).val(
                settings.data[questionId].a[answerId]
            );

            //Add answer to the right-answer select box
            $('#right_answer_select_' + questionId).append('<option value="' + answerId + '">#' + (answerId+1) + '</option>');
        },

        _clearQuestions: function() {
            $(this).find('.question-block').remove();
        },

        _render: function() {
            //Clear questions
            $(this).quizBuilder('_clearQuestions');

            //Get data
            var settings = $(this).data('quizBuilder');

            //render each question
            var $self = $(this);
            $.each(settings.data, function(questionId, value) {
                $self.quizBuilder('_renderQuestionBlock', questionId);
            });

        },

        /**
         * Load data from HTML
         * @private
         */
        _loadHTMLValues: function() {
            var settings = $(this).data('quizBuilder');

            //1. Go over each question
            $.each(settings.data, function(questionId, value) {


                //2. get right answer
                settings.data[questionId]['ra'] = $('#right_answer_select_' + questionId).val();
                //3. get question text
                settings.data[questionId]['q'] = $('#question_text_' + questionId).val();

                //4. Go over each answer
                $.each(settings.data[questionId].a, function(answerId, valueA) {
                    settings.data[questionId].a[answerId] = $('#answer_text_' + questionId + '_' + answerId).val();
                });
            });

            //5. store
            $(this).data('quizBuilder', settings);
        },


        /**
         * Add triggers to buttons
         * @private
         */
        _initBehavior: function() {
            var self = $(this);

            //Init add question
            $(this).find('.add-question').click(function(){
                self.quizBuilder('addQuestion');
                return false;
            });

            //Init add answer
            $(this).find('.add-answer').click(function(){
                //Get questionId
                var questionBlockQuestionId = $(this).closest('.question-block').attr('id');
                questionId = questionBlockQuestionId.split('_');

                self.quizBuilder('addAnswer', questionId[2]); //Set question ID as parameter
                return false;
            });

            //Init delete question
            $(this).find('.delete-question').click(function(){
                //Get question id
                var questionBlockQuestionId = $(this).closest('.question-block').attr('id');
                questionId = questionBlockQuestionId.split('_');

                self.quizBuilder('deleteQuestion', questionId[2]);
                return false;
            });

            //Init delete answer
            $(this).find('.delete-answer').click(function(){
                //Get answer id
                var id = $(this).attr('id');
                data = id.split('_');

                self.quizBuilder('deleteAnswer', data[2], data[3]);
                return false;
            });

            //Init add question
            $(this).find('.save-quiz').click(function(){
                //TODO:
                return false;
            });
        },

        /**
         * Load view templates
         * @private
         */
        _loadTemplates: function() {
            //Add templates
            var settings = $(this).data('quizBuilder');
            settings.templates = {
                questionList: $(this).find('.question-list'),
                questionBlock: $(this).find('.question-block').clone(true),
                answerBlock: $(this).find('.answer-block').clone(true)
            };
            $(this).data('quizBuilder', settings);
        },

        addQuestion: function() {

            var settings = $(this).data('quizBuilder');

            //Add new question
            var questionId = settings.data.length;
            var questionData = {
                qId: questionId,
                q: null,
                a: [],
                ra: null
            };
            settings.data[questionId] = questionData;
            $(this).data('quizBuilder', settings);

            $(this).quizBuilder('_renderQuestionBlock', questionId);
        },

        addAnswer: function( questionId ) {

            var settings = $(this).data('quizBuilder');

            //Add a new answer
            var newAnswerId = settings.data[questionId].a.length;
            settings.data[questionId].a[newAnswerId] = "";
            $(this).data('quizBuilder', settings);

            $(this).quizBuilder('_renderAnswerBlock', questionId, newAnswerId);
        },

        deleteQuestion: function(questionId) {
            //Store data
            $(this).quizBuilder('_loadHTMLValues');

            //Remove answer from data
            var settings = $(this).data('quizBuilder');
            settings.data.splice(questionId, 1);

            $(this).data('quizBuilder', settings); //store

            //Render the new view
            $(this).quizBuilder('_render');
        },

        deleteAnswer: function(questionId, answerId) {
            //Store data
            $(this).quizBuilder('_loadHTMLValues');

            //Remove answer from data
            var settings = $(this).data('quizBuilder');
            settings.data[questionId].a.splice(answerId, 1);

            $(this).data('quizBuilder', settings); //store

            //Render the new view
            $(this).quizBuilder('_render');
        },

        init : function( settings ) {
            var $this = $(this),
                data = $this.data('quizBuilder');

            if(!settings) {
                settings = {};
            }
            if(!settings['data']) {
                settings.data = [];
            }


            // If the plugin hasn't been initialized yet
            if ( ! data ) {
                $(this).data('quizBuilder', settings);
            }

            $(this).quizBuilder('_initBehavior');
            $(this).quizBuilder('_loadTemplates');
            $(this).quizBuilder('_render');
        }
    };

    $.fn.quizBuilder = function( method ) {
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.tooltip' );
        }
    };

})( jQuery );