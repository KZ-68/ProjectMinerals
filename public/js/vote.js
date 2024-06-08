$(document).ready(function() {
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
                            'user' : arrowUp.dataset.user,
                            'commentSlug': arrowUp.dataset.commentslug,
                            'upvote' : true
                        }
                });
                addUpvote.done(function(response){
                    let scoreCount = comment.getElementsByClassName("score-count");
                    scoreCount[0].textContent = response.score;
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
                            'user' : arrowDown.dataset.user,
                            'commentSlug': arrowDown.dataset.commentslug,
                            'downvote' : true
                        }
                });
                addDownvote.done(function(response){
                    let scoreCount = comment.getElementsByClassName("score-count");
                    scoreCount[0].textContent = response.score;
                });
            })
        });
    })
    
});
