<?php

use MediaWiki\MediaWikiServices;
use Wikimedia\Rdbms\DBConnRef;
use Wikimedia\Rdbms\IMaintainableDatabase;

class ApiStarRating extends ApiBase {

    public function execute() {
    	
        $params = $this->extractRequestParams();
        $user = $this->getUser();

        $allow_anonymous = $params['allow_anonymous'];
		$allow_anonymous = $allow_anonymous === true || $allow_anonymous === 'true';

        // ログインチェック
        if ( !$allow_anonymous && !$user->isRegistered() ) { $this->dieWithError( 'apierror-mustbeloggedin', 'not logged in' ); }

        $pageId = intval( $params['pageid'] );
        $userId = $user->getId();
        $tagId =  $params['tagid'];
        $rating = intval( $params['rating'] );
        if ($user->isAnon()) {
            $ip_address = $this->getRequest()->getIP();
            $userId = md5( $ip_address ); 
        }

        $dbw = MediaWikiServices::getInstance()->getConnectionProvider()->getPrimaryDatabase();

        $sql = sprintf(
            "INSERT INTO star_rating (page_id, user_id, tag_id, rating, timestamp)
            VALUES (%s, %s, %s, %s, NOW())
            ON DUPLICATE KEY UPDATE
            rating = VALUES(rating),
            timestamp = NOW()",
            $dbw->addQuotes( $pageId ),
            $dbw->addQuotes( $userId ),
            $dbw->addQuotes( $tagId ),
            $dbw->addQuotes( $rating )
        );

        $dbw->query( $sql, __METHOD__ );

        $this->getResult()->addValue( null, $this->getModuleName(), [
            'result' => 'success'
        ] );
    }

    public function getAllowedParams() {
        return [
            'pageid' => [
                ApiBase::PARAM_TYPE => 'integer',
                ApiBase::PARAM_REQUIRED => true
            ],
            'tagid' => [
                ApiBase::PARAM_TYPE => 'string',
                ApiBase::PARAM_REQUIRED => true
            ],
            'rating' => [
                ApiBase::PARAM_TYPE => 'integer',
                ApiBase::PARAM_REQUIRED => true
            ],
            'allow_anonymous' => [
                ApiBase::PARAM_TYPE => 'boolean',
                ApiBase::PARAM_REQUIRED => false
            ]
        ];
    }

    public function needsToken() {
        return 'csrf';
    }

    public function isWriteMode() {
        return true;
    }

    public function getExamplesMessages() {
        return [
            'action=starrating&pageid=123&tagid=1&rating=5&token=XYZ' => 'Submit a star rating'
        ];
    }
}