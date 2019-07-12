<?php 

// TYPES

// 9 -  Straight Flush
// 8 -  4 of a kind
// 7 -  Full House
// 6 -  Flush
// 5 -  Straight
// 4 -  3 of a kind
// 3 -  2 Pair
// 2 -  1 Pair
// 1 -  High Card


class Hand{

	function __construct($cards, $board){
		$this->hand = $cards;
		$this->boardHand = array_merge($cards, $board);
		$this->type = 1;
		$this->winner = false;

		$this->ranks = [
			14 => 0,
			13 => 0,
			12 => 0,
			11 => 0,
			10 => 0,
			9 => 0,
			8 => 0,
			7 => 0,
			6 => 0,
			5 => 0,
			4 => 0,
			3 => 0,
			2 => 0,
		];

		$this->suits = [
			'C' => 0,
			'D' => 0,
			'H' => 0,
			'S' => 0,
		];

		$this->flushSuit = null;
		$this->lowestStraightCard = null;

		$this->quads = [];
		$this->trips = [];
		$this->pairs = [];
		$this->kickers = [];

	}

	function sortRanks(){
		
		foreach($this->boardHand as $card){
			$rank = substr($card, 0, 1);
			$rank = $this->convertRankToInteger($rank);
			$this->ranks[$rank] ++;
		}

	}

	function convertRankToInteger($str){
		switch($str){
			case 'A':
				return 14;
				break;
			case 'K':
				return 13;
				break;
			case 'Q':
				return 12;
				break;
			case 'J':
				return 11;
				break;
			case 'T':
				return 10;
				break;
			default :
				return (int)$str;
				break;
		}
	}


	function sortSuits(){
		
		foreach($this->boardHand as $card){
			$suit = substr($card, 1);
			$this->suits[$suit] ++;
		}

	}

	function sortPairs(){
		
		foreach($this->ranks as $key => $value){

			switch($value){
				case 4:
					$this->quads[] = $key;
					break; 
				case 3:
					$this->trips[] = $key; 
					break;
				case 2:
					$this->pairs[] = $key;
					break;
				case 1:
					$this->kickers[] = $key;
					break; 
			}
		}
	}



	function isFlush(){

		foreach($this->suits as $key => $value){
			if($value >= 5){
				$this->flushSuit = $key;
				return true;
			}
		}

		return false;
	}


	function isStraight(){

		$streak = 0;

		foreach($this->ranks as $key => $value){
			if($value > 0){
				$streak ++;
				if($streak === 5){
					$this->lowestStraightCard = $key;
					return true;
				}
			} else{
				$streak = 0;
			}
		}

		return false;
	}

	function isQuads(){
		if (count($this->quads) > 0) return true;
			else return false;
	}

	function isTrips(){
		if (count($this->trips) > 0){
			
			// check for boat
			return true;

		} else return false;
	}

}

$board = ['JS', 'JH', 'JC', 'JD', 'QS'];
$hand = new Hand(['KD', 'KS'], $board);

$hand->sortRanks();
$hand->sortSuits();
$hand->sortPairs();

var_dump($hand->ranks);

// Check for flush (then straight flush)
// Check for 4
// Check for 3 (then full house)
// Check for straight
// Two pair
// Pair

var_dump('FLUSH: ' . $hand->isFlush());
var_dump('STRAIGHT: ' . $hand->isStraight());

var_dump('LOWEST STRAIGHT CARD: ' . $hand->lowestStraightCard);
var_dump('FLUSH SUIT: ' . $hand->flushSuit);

var_dump($hand->trips);
var_dump($hand->pairs);

var_dump($hand->isQuads());








