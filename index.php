<?php 

class Hand{

	function __construct($cards, $board){
		$this->hand = $cards;
		$this->boardHand = array_merge($cards, $board);
		$this->value = 1;
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
		$this->lowestStraightFlushCard = null;
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
		$this->flushCards = $flushRanks;

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

			if($streak === 5){ // found straight flush
				$this->lowestStraightFlushCard = [$card];
				return true;
			}

			$lastValue = $card;
		}

		if($streak === 4 && $flushRanks[0] === 14){ // Ace-low straight flush exception
			if(array_values(array_slice($flushRanks, -1))[0] === 2){ // last item in array is 2 so is straight flush
				$this->lowestStraightFlushCard = [1];
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
				if($streak === 5){ // found straight
					$this->lowestStraightCard = [$key];
					return true;
				}
			} else{
				$streak = 0;
			}
		}

		if($streak === 4 && $this->ranks[14] > 0){ // Ace-low straight exception
			$this->lowestStraightCard = [1];
			return true;
		}

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

	function computeValue(){

		$isFlush = $this->isFlush();
		$isTrips = $this->isTrips();

		if($isFlush){ 
			if($this->isStraightFlush()) return 9;
		}

		if($this->isQuads()) return 8;
		
		if($isTrips){
			if($this->isBoat()) return 7;
		}

		if($isFlush) return 6;
		if($this->isStraight()) return 5;
		if($isTrips) return 4;
		if($this->isTwoPair()) return 3;
		if($this->isPair()) return 2;

		return 1;

	}

}


$board = ['6S', '5S', '7S', '9S', '8S'];
$hands = [['AS', 'JC'], ['4S', 'KH']];

$computedHands = [];

foreach($hands as $key => $hand){
	$playerHand = new Hand($hand, $board);
	$playerHand->sortRanks();
	$playerHand->sortSuits();
	$playerHand->sortPairs();
	$playerHand->value = $playerHand->computeValue();
	$computedHands[] = $playerHand;
}

$possibleWinners = [];
$bestHandValue = 0;

foreach($computedHands as $hand){

	$handValue = $hand->value;
	
	if($handValue > $bestHandValue){
		$possibleWinners = [$hand];
		$bestHandValue = $handValue;
		continue;
	}

	if($handValue === $bestHandValue){
		$possibleWinners[] = $hand;
	}

}

if(count($possibleWinners) === 1){
	var_dump($possibleWinners);
} else{
	//breakTies($possibleWinners);
	var_dump(breakTies($possibleWinners));
}



function breakTies($hands){

	switch($hands[0]->value){

		case 9:
			$winners = checkArrays($hands, 'lowestStraightFlushCard', 1);
			break;
		case 8:
			$winners = checkArrays($hands, 'quads', 1, ['trips', 'pairs', 'kickers'], 1);
			break;
		case 7:
			$winners = breakBoatTie($hands);
			break;
		case 6:
			$winners = checkArrays($hands, 'flushCards', 5);
			break;
		case 5:
			$winners = checkArrays($hands, 'lowestStraightCard');
			break;
		case 4:
			$winners = checkArrays($hands, 'trips', 1, ['kickers'], 2);
			break;
		case 3:
			$winners = checkArrays($hands, 'pairs', 2, ['kickers'], 1);
			break;
		case 2:
			$winners = checkArrays($hands, 'pairs', 1, ['kickers'], 3);
			break;
		case 1:
			$winners = breakHighCardTie($hands, ['kickers'], 5);
			break;

	}

	return $winners;
}

function checkArrays($hands, $toCompare, $depth = 1, $kickers = null, $kickerDepth = null){

	for($i = 0; $i < $depth; $i++){

		$winners = [];
		$best = 0;

		foreach($hands as $hand){

			if($hand->{$toCompare}[$i] > $best){
				$winners = [$hand];
				$best = $hand->{$toCompare}[$i];
				continue;
			}

			if($hand->{$toCompare}[$i] === $best){
				$winners[] = $hand;
			}

		}

		if(count($winners) === 1) return $winners;

	}

	return $winners;

}








