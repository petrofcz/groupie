<?php

namespace PczTests\Groupie;

class Order
{
	/** @var  int */
	protected $price;

	/** @var  Category */
	protected $category;

	/** @var  Partner */
	protected $partner;

	/**
	 * Order constructor.
	 * @param int $price
	 * @param Category $category
	 * @param Partner $partner
	 */
	public function __construct($price, Category $category, Partner $partner = null)
	{
		$this->price = $price;
		$this->category = $category;
		$this->partner = $partner;
	}

	/**
	 * @return int
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * @return Category
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * @return Partner
	 */
	public function getPartner()
	{
		return $this->partner;
	}



}