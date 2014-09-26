<?php

class erLhcoreClassModelFaq {

	public function getState()
	{
		return array(
				'id'         => $this->id,
				'question'   => $this->question,
				'answer'     => $this->answer,
				'url'        => $this->url,
				'active'     => $this->active,
				'has_url'    => $this->has_url,
				'is_wildcard'=> $this->is_wildcard,
				'email'   	 => $this->email,
				'identifier' => $this->identifier
		);
	}

	public function setState( array $properties )
	{
		foreach ( $properties as $key => $val )
		{
			$this->$key = $val;
		}
	}

	public static function getCount($params = array())
	{
		$session = erLhcoreClassFaq::getSession();
		$q = $session->database->createSelectQuery();
		$q->select( "COUNT(id)" )->from( "lh_faq" );

		if (isset($params['filter']) && count($params['filter']) > 0)
		{
			$conditions = array();

			foreach ($params['filter'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
			}

			$q->where(
					$conditions
			);
		}

		$stmt = $q->prepare();
		$stmt->execute();
		$result = $stmt->fetchColumn();

		return $result;
	}

	public static function getList($paramsSearch = array())
	{
		$paramsDefault = array('limit' => 32, 'offset' => 0);

		$params = array_merge($paramsDefault,$paramsSearch);

		$session = erLhcoreClassFaq::getSession();
		$q = $session->createFindQuery( 'erLhcoreClassModelFaq' );

		$conditions = array();

		if (isset($params['filter']) && count($params['filter']) > 0)
		{
			foreach ($params['filter'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->eq( $field, $q->bindValue($fieldValue) );
			}
		}

		if (isset($params['filterin']) && count($params['filterin']) > 0)
		{
			foreach ($params['filterin'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->in( $field, $fieldValue );
			}
		}

		if (isset($params['filterlt']) && count($params['filterlt']) > 0)
		{
			foreach ($params['filterlt'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->lt( $field, $q->bindValue($fieldValue) );
			}
		}

		if (isset($params['filtergt']) && count($params['filtergt']) > 0)
		{
			foreach ($params['filtergt'] as $field => $fieldValue)
			{
				$conditions[] = $q->expr->gt( $field,$q->bindValue( $fieldValue ));
			}
		}

		if (count($conditions) > 0)
		{
			$q->where(
					$conditions
			);
		}

		$q->limit($params['limit'],$params['offset']);

		$q->orderBy(isset($params['sort']) ? $params['sort'] : 'id DESC' );

		$objects = $session->find( $q );

		return $objects;
	}

	public static function fetch($id) {
		$Faq = erLhcoreClassFaq::getSession()->load( 'erLhcoreClassModelFaq', (int)$id );
		return $Faq;
	}

	public function saveThis()
	{
		if ($this->url != '') {

			$matchStringURL = '';

			$parts = parse_url($this->url);
			if (isset($parts['path'])) {
				$matchStringURL = $parts['path'];
			}

			if (isset($parts['query'])) {
				$matchStringURL .= '?'.$parts['query'];
			}

			$this->url = $matchStringURL;
			$this->has_url = 1;

			if (substr($this->url, -1) == '*'){
				$this->is_wildcard = 1;
			}

		} else {
			$this->has_url = 0;
			$this->is_wildcard = 0;
		}



		erLhcoreClassFaq::getSession()->saveOrUpdate($this);
	}

	public function removeThis() {
		erLhcoreClassFaq::getSession()->delete( $this );
	}

	public $id = null;
	public $question = '';
	public $answer = '';
	public $url = '';
	public $active = 1;
	public $has_url = 0;
	public $is_wildcard = 0;
	public $email = '';
	public $identifier = '';	
}

?>