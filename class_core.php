<?php
/**
* Profanity Filter Version 1.0.0
* Copyright (C) 2015 Matt Kent
*/

if ( ! defined('IN_PROFANITY')) exit;

define('SCRIPT_V', '1.0.0');

class Profanity
{

	private $_filter = '*';
	
	private $_blacklist = array(
		'fuck', 'cunt', 'shit', 'shitter',
		'fucking', 'dick', 'dickhead', 'knobhead',
		'bellend', 'cock', 'prick', 'wanker',
		'twat', 'piss', 'slut', 'slag',
		'whore', 'bitch', 'fag', 'clunge',
		'ass', 'arse', 'damn', 'bastard', 
		'crap', 'fucker', 'bollock', 'bollocks',
		);

	private $_blacklist_metahpone = array(
		'shit', 'motherfucker', 'fuk', 'wank',
		'fukin', 'fuckin', 'fucking', 'bastard',
		'ass', 'asshole', 'arsehole', 'faggot',
		'nigger', 'nigga', 'tosser', 'tossa',
		'bugger', 'cunt',
		);

	private $_symbols = array('1', '3', '5', '$', '(', '@', '!');
	private $_letters = array('a', 'e', 'i', 'o', 'c', 'i', 's');
	private $_spaces = '/(\s{1,}|\-{1,}|\_{1,}|\.{1,}|\|{1,})/';

	private $_string;
	private $_orignal;
	private $_output;

	public function __construct($string)
	{
		$this->_orignal = $this->_string = strtolower($string);

		$this->similar_to();
		$this->letters_concurrent();
		$this->direct_match();
		$this->replace_space();

		return $this->output();
	}

	private function similar_to()
	{
		$words = explode(' ', $this->_string);

		foreach ($words as $word)
		{
			foreach ($this->_blacklist_metahpone as $word_met)
			{
				if (metaphone($word) === metaphone($word_met))
				{
					$this->_string = str_replace($word, str_repeat("{$this->_filter}", strlen($word)), $this->_string);
				}
			}
		}
	}

	private function letters_concurrent()
	{
		$words = explode(' ', $this->_string);

		foreach ($words as $key => $value)
		{
			$a = preg_replace('/(.)\\1+/i', '$i', $value);

			if (in_array($a, $this->_blacklist))
			{
				$this->_string = str_replace($value, $a, $this->_string);
			}

			$lengths = array_map('strlen', $this->_blacklist);

			if (strlen($value) < min($lengths))
			{
				$s_word = $words[$key - 1] . ' ' . $value . ' ' . $words[$key + 1];
				$a = preg_replace('/(.)\\1+/i', '$1', str_replace(' ', '', $s_word));

				if (in_array($a, $this->_blacklist))
				{
					$this->_string = str_replace(trim($s_word), str_repeat("{$this->_filter}", strlen($a)), $this->_string);
				}
			}
		}
	}

	public function direct_match()
	{
		foreach ($this->_blacklist as $word)
		{
			$this->_string = str_replace($word, str_repeat("{$this->_filter}", strlen($word)), $this->_string);
		}
	}

	public function replace_space()
	{
		$this->_string = preg_replace($this->_spaces, '-', $this->_string);

		foreach ($this->_blacklist as $word)
		{
			$letters = str_split($word);
			$dashed_w = implode('-', $letters);

			$this->_string = preg_replace('/' . $dashed_w . '/i', str_repeat("{$this->_filter}", strlen($word)), $this->_string);
		}
		$this->_string = str_replace('-', ' ', $this->_string);
	}

	private function output()
	{
		$this->_output = array(
			'original' => $this->_orignal,
			'filtered' => $this->_string
			);

		if ($this->_string !== $this->_orignal)
		{
			return json_encode($this->_output);
		}
	}

	public function __toString()
	{
		return json_encode($this->_output);
	}
}