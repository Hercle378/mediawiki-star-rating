const baseUrl = mw.config.get('starRatingBaseUrl');

const path_img_star_0_0 = baseUrl + 'images/star_zero.png';
const path_img_star_0_5 = baseUrl + 'images/star_half.png';
const path_img_star_1_0 = baseUrl + 'images/star_one.png';

document.querySelectorAll('.star-rating').forEach(div_rating => {

  const stars = div_rating.querySelectorAll('span.star');
  div_rating.setAttribute("your_rating", "-1");

  let func_get_star = function (span_each, tag_rating_base) {

    let r_each = span_each.getAttribute("rating");
    let r_base = tag_rating_base.getAttribute("rating");

    let path_img = path_img_star_0_0;
    let tag_img = span_each.querySelector('img');

    if (r_each - 0.75 <= r_base && r_each - 0.25 > r_base) {
      path_img = path_img_star_0_5;
    } else if (r_each - 0.25 <= r_base) {
      path_img = path_img_star_1_0;
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

      let userId = mw.config.get('wgUserId');
      let allow_anonymous = div_rating.getAttribute("allow_anonymous") == "true"; 
      if (!allow_anonymous && !userId ) {
        alert('Sorry, you need to log in before you can vote.');
        return;
      }

      let rating = star.getAttribute("rating");
      div_rating.setAttribute("your_rating", rating);
      div_rating.querySelector(".span_thanks_voting").style.display = "block";
      div_rating.querySelector(".span_your_rating").textContent = rating;
      send_rating(star.getAttribute("rating"), 
                  div_rating.getAttribute("tag_id"),
                  allow_anonymous);
    });

  });

  // tooltip - voting distribution
  {
    let span_point = div_rating.querySelector('span.star_point');
    let tooltip = div_rating.querySelector('span.tooltip_rating');

    let dist = JSON.parse(tooltip.getAttribute("distribution"));
    let total_count = tooltip.getAttribute("total");

    for (let i = 5; i >= 1; i--) {
      for (let j = 0; j < 5; j++) {
        let tag_img = document.createElement('img');
        tag_img.setAttribute('width', '16');
        tag_img.setAttribute('height', '16');
        tag_img.src = i > j ? path_img_star_1_0 : path_img_star_0_0;
        tooltip.appendChild(tag_img)
      }
      let dist_each = dist[i];
      let dist_percent = total_count != 0 ? Math.floor((dist_each / total_count) * 100) : 0; 
      tooltip.appendChild(document.createTextNode(` ${dist_percent} % (${dist_each})`));
      tooltip.appendChild(document.createElement('br'));
    }
  
    span_point.addEventListener('mouseenter', function (e) {
      tooltip.style.left = (e.pageX + 10) + 'px';
      tooltip.style.top = (e.pageY + 10) + 'px';
      tooltip.style.display = 'block';
    });

    span_point.addEventListener('mouseleave', function () {
      tooltip.style.display = 'none';
    });
  }

});


function send_rating(rating, tagId, allow_anonymous) {

  let pageId = mw.config.get('wgArticleId');

  const api = new mw.Api();
  api.getToken('csrf').then(function (token) {
    return api.post({
      action: 'starrating',
      format: 'json',
      token: token,
      pageid: pageId,
      tagid: tagId,
      rating: rating,
      allow_anonymous: allow_anonymous
    });
  }).done(function (data) {
    // 評価送信完了 
  }).fail(function (err) {
    alert('Failed to submit rating:' + err);
    console.error('Failed to submit rating:', err);
  });

}

