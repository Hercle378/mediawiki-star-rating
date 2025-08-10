const baseUrl = mw.config.get( 'starRatingBaseUrl' );

document.querySelectorAll('.star-rating').forEach(div_rating => {
  const stars = div_rating.querySelectorAll('span.star');
  div_rating.setAttribute("your_rating", "-1");
  
  let func_get_star = function (span_each, tag_rating_base) {

    let r_each = span_each.getAttribute("rating");
    let r_base = tag_rating_base.getAttribute("rating");

    let path_img_base = baseUrl + 'images/star_one.png';
		let path_img = path_img_base.replace('_one', '_zero'); // 0
    let tag_img = span_each.querySelector('img');

    if (r_each - 0.75 <= r_base && r_each - 0.25 > r_base) {
      path_img = path_img_base.replace('_one', '_half'); // 0.5
    } else if (r_each - 0.25 <= r_base) {
      path_img = path_img_base; // 1
    }

    tag_img.src = path_img;

  };

  stars.forEach((star, i) => {
    star.addEventListener('mouseover', () => {
      if (div_rating.getAttribute("your_rating") > 0) return;
      stars.forEach((s) => { func_get_star(s, star); });
    });
    star.addEventListener('mouseleave', () => {
      if (div_rating.getAttribute("your_rating") > 0) return;
      stars.forEach((s) => { func_get_star(s, div_rating); });
    });
    star.addEventListener('click', function () {
      if (div_rating.getAttribute("your_rating") > 0) return;
      let rating = star.getAttribute("rating");
      div_rating.setAttribute("your_rating", rating);
      div_rating.querySelector(".span_thanks_voting").style.display = "block";
      div_rating.querySelector(".span_your_rating").textContent = rating;
      send_rating(star.getAttribute("rating"), div_rating.getAttribute("tag_id"));
    });
  });

});


function send_rating(rating, tagId) {

  let pageId = mw.config.get('wgArticleId'); 
  let userId = mw.config.get('wgUserId');

  const api = new mw.Api();
  api.getToken('csrf').then(function(token) {
      return api.post({
        action: 'starrating',
        format: 'json',
        token: token,
        pageid: pageId,
        userid: userId,
        tagid: tagId,
        rating: rating
      });
  }).done(function(data) {
    // 評価送信完了 
  }).fail(function(err) {
    alert('Failed to submit rating:' + err);
    console.error('Failed to submit rating:', err);
  });

}

// js 読み込まれ時実行
(function () {

  const span_point = document.getElementById('star_point');
  const tooltip = document.getElementById('rating-tooltip');

  span_point.addEventListener('mouseenter', function (e) {

    let dist = JSON.parse(tooltip.getAttribute("distribution"));
    let html = '';

      for (let i = 5; i >= 1; i--) {
          const stars = '★'.repeat(i) + '☆'.repeat(5 - i);
          html += `${stars} (${dist[i] || 0})<br>`;
      }

      tooltip.innerHTML = html;
      tooltip.style.left = (e.pageX + 10) + 'px';
      tooltip.style.top = (e.pageY + 10) + 'px';
      tooltip.style.display = 'block';
  });

  span_point.addEventListener('mouseleave', function () {
      tooltip.style.display = 'none';
  });

})();

