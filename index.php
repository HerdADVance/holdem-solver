<?php 

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
			'C' => [],
			'D' => [],
			'H' => [],
			'S' => [],
		];

		$this->flushSuit = null;
		$this->flushCards = [];
		$this->lowestStraightCard = null;
		$this->boatHigh = null;
		$this->boatLow = null;

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
			$rank = substr($card, 0, 1);
			$suit = substr($card, 1);
			$this->suits[$suit][] = $this->convertRankToInteger($rank);;
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
			if(count($value) >= 5){
				$this->flushSuit = $key;
				return true;
			}
		}

		return false;
	}

	function isStraightFlush(){ // Will only be ran if isFlush is true

		$flushRanks = $this->suits[$this->flushSuit];
		rsort($flushRanks);

		$streak = 1;
		$lastValue = null;

		foreach($flushRanks as $card){
			
			if($lastValue === null){
				$lastValue = $card;
				continue;
			}

			if($lastValue === $card + 1){
				$streak ++;
			} else{
				$lastValue = null;
				$streak = 1;
			}

			if($streak === 5) return true;

			$lastValue = $card;
		}

		if($streak === 4 && $flushRanks[0] === 14){ // Ace-low straight flush exception
			if(array_values(array_slice($flushRanks, -1))[0] === 2) return true; // last item in array is 2 so is straight flush
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

		if($streak === 4 && $this->ranks[14] > 0) return true; // Ace-low straight exception

		return false;
	}

	function isQuads(){
		if (count($this->quads) > 0) return true;
			else return false;
	}

	function isTrips(){
		if (count($this->trips) > 0) return true;
			else return false;
	}

	function isBoat(){ // will only be ran if isTrips returns true
		
		if($this->trips[1]){
			$this->boatHigh = $this->trips[0];
			$this->boatLow = $this->trips[1];
			return true;
		}
		
		if($this->pairs[0]){
			$this->boatHigh = $this->trips[0];
			$this->boatLow = $this->pairs[0];
			return true;
		}

		return false;
	}

	function isTwoPair(){
		if(count($this->pairs) >= 2) return true;
			return false;
	}

	function isPair(){
		if(count($this->pairs) == 1) return true;
			return false;
	}

}

$board = ['2S', '3S', '4S', '5S', 'AH'];
$hand = new Hand(['KS', 'JC'], $board);

$hand->sortRanks();
$hand->sortSuits();
$hand->sortPairs();
var_dump(computeHand($hand));

function computeHand($hand){

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

	$isFlush = $hand->isFlush();
	$isTrips = $hand->isTrips();

	if($isFlush){ 
		if($hand->isStraightFlush()) return 9;
	}

	if($hand->isQuads()) return 8;
	
	if($isTrips){
		if($hand->isBoat()) return 7;
	}

	if($isFlush) return 6;
	if($hand->isStraight()) return 5;
	if($isTrips) return 4;
	if($hand->isTwoPair()) return 3;
	if($hand->isPair()) return 2;

	return 1;

}









