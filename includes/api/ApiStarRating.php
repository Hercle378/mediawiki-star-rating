<?php

use MediaWiki\MediaWikiServices;
use Wikimedia\Rdbms\DBConnRef;
use Wikimedia\Rdbms\IMaintainableDatabase;

class ApiStarRating extends ApiBase {

    public function execute() {
    	
        $params = $this->extractRequestParams();
        $user = $this->getUser();

        // 必須チェック
        if ( !$user->isRegistered() ) { $this->dieWithError( 'apierror-mustbeloggedin', 'notloggedin' ); }

        $pageId = intval( $params['pageid'] );
        $userId = $user->getId();
        $tagId =  $params['tagid'];
        $rating = intval( $params['rating'] );

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
            'userid' => [
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