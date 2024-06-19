$(document).ready(function() {
    let discussion = document.querySelector(".discussion-card-score");
    let arrowUpDiscussion = discussion.querySelectorAll(".score-arrow-up");
    let arrowDownDiscussion = discussion.querySelectorAll(".score-arrow-down");

    arrowUpDiscussion.forEach((arrowUp) => {
        arrowUp.addEventListener("click", () => {
            let addUpvote = 
            $.ajax({
                type: 'POST',
                url: window.location.href+"/upvote",
                data: {
                        'discussionSlug': arrowUp.dataset.discussionslug,
                        'upvote' : true
                    }
            });
            addUpvote.done(function(response){
                let scoreCount = discussion.getElementsByClassName("score-count");
                if(response.alert) {
                    let alertError = $("#alert-error");
                    alertError.fadeIn().delay(5000).text(response.alert).fadeOut()
                } else if(response.error_login) {
                    let alertError = $("#alert-error");
                    alertError.fadeIn().delay(5000).text(response.error_login).fadeOut()
                } else {
                    scoreCount[0].textContent = response.score;
                }
            });
        })
    })

    arrowDownDiscussion.forEach((arrowDown) => {
        arrowDown.addEventListener("click", () => {
            let addDownvote = 
            $.ajax({
                type: 'POST',
                url: window.location.href+"/downvote",
                data: {
                        'discussionSlug': arrowDown.dataset.discussionslug,
                        'downvote' : true
                    }
            });
            addDownvote.done(function(response){
                let scoreCount = discussion.getElementsByClassName("score-count");
                if(response.alert) {
                    let alertError = $("#alert-error");
                    alertError.fadeIn().delay(5000).text(response.alert).fadeOut()
                } else if(response.error_login) {
                    let alertError = $("#alert-error");
                    alertError.fadeIn().delay(5000).text(response.error_login).fadeOut()
                } else {
                    scoreCount[0].textContent = response.score;
                }
            });
        })
    })

    let commentList = document.querySelectorAll(".comment-card-score");

    commentList.forEach((comment) => {
        let arrowUpList = comment.querySelectorAll(".score-arrow-up");
        let arrowDownList = comment.querySelectorAll(".score-arrow-down");

        arrowUpList.forEach((arrowUp) => {

            arrowUp.addEventListener("click", function() { 
                let addUpvote =
                $.ajax({
                    type: 'POST',
                    url: window.location.href+"/upvote",
                    data: {
                            'commentSlug': arrowUp.dataset.commentslug,
                            'upvote' : true
                        }
                });
                addUpvote.done(function(response){
                    let scoreCount = comment.getElementsByClassName("score-count");
                    if(response.alert) {
                        let alertError = $("#alert-error");
                        alertError.fadeIn().delay(5000).text(response.alert).fadeOut()
                    } else if(response.error_login) {
                        let alertError = $("#alert-error");
                        alertError.fadeIn().delay(5000).text(response.error_login).fadeOut()
                    } else {
                        scoreCount[0].textContent = response.score;
                    }
                });
            })
        });

        arrowDownList.forEach((arrowDown) => {

            arrowDown.addEventListener("click", function() { 
                let addDownvote =
                $.ajax({
                    type: 'POST',
                    url: window.location.href+"/downvote",
                    data: {
                            'commentSlug': arrowDown.dataset.commentslug,
                            'downvote' : true
                        }
                });
                addDownvote.done(function(response){
                    let scoreCount = comment.getElementsByClassName("score-count");
                    if(response.alert) {
                        let alertError = $("#alert-error");
                        alertError.fadeIn().delay(5000).text(response.alert).fadeOut()
                    } else if(response.error_login) {
                        let alertError = $("#alert-error");
                        alertError.fadeIn().delay(5000).text(response.error_login).fadeOut()
                    } else {
                        scoreCount[0].textContent = response.score;
                    }
                });
            })
        });
    })

    let responseComments = document.querySelectorAll(".response-comment-card-score");

    responseComments.forEach((responseComment)=> {
        let responseArrowUpList = responseComment.querySelectorAll(".score-arrow-up");
        let responseArrowDownList = responseComment.querySelectorAll(".score-arrow-down");

        responseArrowUpList.forEach((arrowUp) => {

            arrowUp.addEventListener("click", function() { 
                let addUpvote =
                $.ajax({
                    type: 'POST',
                    url: window.location.href+"/upvote",
                    data: {
                            'commentSlug': arrowUp.dataset.commentslug,
                            'upvote' : true
                        }
                });
                addUpvote.done(function(response){
                    let scoreCount = responseComment.getElementsByClassName("score-count");
                    if(response.alert) {
                        let alertError = $("#alert-error");
                        alertError.fadeIn().delay(5000).text(response.alert).fadeOut()
                    } else if(response.error_login) {
                        let alertError = $("#alert-error");
                        alertError.fadeIn().delay(5000).text(response.error_login).fadeOut()
                    } else {
                        scoreCount[0].textContent = response.score;
                    }
                });
            })
        });

        responseArrowDownList.forEach((arrowDown) => {

            arrowDown.addEventListener("click", function() { 
                let addDownvote =
                $.ajax({
                    type: 'POST',
                    url: window.location.href+"/downvote",
                    data: {
                            'commentSlug': arrowDown.dataset.commentslug,
                            'downvote' : true
                        }
                });
                addDownvote.done(function(response){
                    let scoreCount = responseComment.getElementsByClassName("score-count");
                    if(response.alert) {
                        let alertError = $("#alert-error");
                        alertError.fadeIn().delay(5000).text(response.alert).fadeOut()
                    } else if(response.error_login) {
                        let alertError = $("#alert-error");
                        alertError.fadeIn().delay(5000).text(response.error_login).fadeOut()
                    } else {
                        scoreCount[0].textContent = response.score;
                    }
                });
            })
        });
    })

    
});
