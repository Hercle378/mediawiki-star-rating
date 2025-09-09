<?php
namespace StarRating;

use Parser;
use PPFrame;
use OutputPage;
use Skin;
use MediaWiki\Installer\DatabaseUpdater;
use MediaWiki\MediaWikiServices;
use MediaWiki\Context\RequestContext;
use MediaWiki\Parser\ParserOutput;

// memo 
//wfDebugLog( "star_rating_log", "log");
//?action=purge

class Hooks {

	public static function onParserFirstCallInit( Parser $parser ) {
        $parser->setHook( 'StarRating', [ self::class, 'renderStarRatingTag' ] );
        return true;
    }

	public static function renderStarRatingTag( $input, array $args, Parser $parser, PPFrame $frame ) {

		// リソースパスの取得 JSへの提供
		$parser->getOutput()->addModules( ['ext.starRating'] );

		// JSに画像のベースURLを渡す（安全な方法）
		global $wgExtensionAssetsPath;
		$baseUrl = $wgExtensionAssetsPath . '/StarRating/';

		// パラメータセット
		$pageId =  $parser->getTitle()->getArticleID();
		$tagId = isset( $args['id'] ) ? htmlspecialchars( $args['id'] ) : null;
		$star_size = self::check_int( $args['star_size'] ?? null , 1, 100, 16 );
		$digits = self::check_int( $args['digits'] ?? null, 0, 4, 1 );
		$clear_cache = $args['clear_cache'] ?? false;
		$clear_cache = $clear_cache === true || $clear_cache === 'true';
		$allow_anonymous = $args['allow_anonymous'] ?? false;
		$allow_anonymous = $allow_anonymous === true || $allow_anonymous === 'true';
		// Disable caching for this tag
		if ( $clear_cache ) $parser->getOutput()->updateCacheExpiry( 0 );

		$res_rating = self::get_rating_info( $pageId, $tagId ); 
		if ( !$res_rating ) return;

		// html
		$count_rate = 'distribution=\'' . json_encode($res_rating['distribution'], JSON_UNESCAPED_UNICODE) . '\'';
		$rating_point = round($res_rating['avg'], $digits); // 小数点 n 位まで
		$total_count = $res_rating['total'];

		$html = '<span class="star-rating" tag_id="' . $tagId . '" rating=' . $rating_point . 
					' allow_anonymous=' . var_export($allow_anonymous, true) .'>';
		$html_image = '<img src="' . $baseUrl . 'images/star_one.png" ' . 
						'width="' . $star_size . '" height="' . $star_size . '">';

		// star images
		for ( $i = 1; $i <= 5; $i++ ) {
			$html_img = str_replace('_one', '_zero', $html_image); // 0
			if ($i - 0.75 <= $rating_point && $i - 0.25 > $rating_point) {
				$html_img = str_replace('_one', '_half', $html_image); // 0.5
			} elseif ($i - 0.25 <= $rating_point) {
				$html_img = $html_image; // 1
			}
			$html .= '<span class="star" rating="' . $i . '">' . $html_img . '</span>';
		}

		$html .= '<span class="star_point"> ' . $rating_point . ' Point(s)!'. 
				 ' (' . $total_count .' vote(s))</span>' . "<br/>";

		$html .= '<span class="span_thanks_voting" style="display: none; color: red; font-weight: bold;">' . 
			'Thank you for your voiting! > Rated <span class="span_your_rating"></span></span>';
			
		// count and ratte
		$html .= '<span class="tooltip_rating" ' . $count_rate . ', total="' . $total_count . '"></span>';
			
		$html .= '</span>';

		return $html;

	}
	
	// Returns the average rating and distribution of ratings for a given page and tag
	// avg, total, distribution
	private static function get_rating_info( $pageId, $tagId ) {

		$db = MediaWikiServices::getInstance()
				->getConnectionProvider()
				->getPrimaryDatabase();

		$res = $db->select(
			'star_rating',
			[
				'page_id',
				'tag_id',
				'total_count' => 'COUNT(*)',
				'avg_rating' => 'AVG(rating)',
				'count_1' => 'SUM(rating = 1)',
				'count_2' => 'SUM(rating = 2)',
				'count_3' => 'SUM(rating = 3)',
				'count_4' => 'SUM(rating = 4)',
				'count_5' => 'SUM(rating = 5)',
			],
			[
				'page_id' => $pageId,
				'tag_id' => $tagId
			],
			__METHOD__,
			[
				'GROUP BY' => 'page_id, tag_id'
			]
		);

		$row = $res->fetchObject();
		if ( !$row ) {
			return [
				'avg' => 0,
				'total' => 0,
				'distribution' => [0, 0, 0, 0, 0]
			];
		}

		$avg = round((float)$row->avg_rating, 4);
		$total = (int)$row->total_count;
		$dist = [
			1 => (int)$row->count_1,
			2 => (int)$row->count_2,
			3 => (int)$row->count_3,
			4 => (int)$row->count_4,
			5 => (int)$row->count_5,
		];

		return [
			'avg' => $avg,
			'total' => $total,
			'distribution' => $dist
		];

	}

    public static function onBeforePageDisplay( OutputPage &$out, Skin &$skin ) {

		$out->addModules( 'ext.starRating' );

		global $wgExtensionAssetsPath;
		$baseUrl = $wgExtensionAssetsPath . '/StarRating/';
		$out->addJsConfigVars( 'starRatingBaseUrl', $baseUrl );

		return true;
	}

	public static function onLoadExtensionSchemaUpdates( DatabaseUpdater $updater ) {
		$updater->addExtensionTable(
		'star_rating',
		__DIR__ . '/../sql/star_rating.sql'
		);
	}

	private static function check_int( $str, $min, $max, $def ): int {

		if (!is_numeric($str)) return $def;
		$value = (int)$str;
		if ($value < $min || $value > $max) return $def;
		return $value;

	}

}


