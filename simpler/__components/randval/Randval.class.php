<?php
/**
 * 
 * This class deals with character drawing.
 * 
 * @package Simpler
 * @subpackage Randval
 * 
 */


    namespace Simpler\Components\Randval;

    class Randval{
        /**
         *
         * Sets the range of the draw:
         * - string,
         * - int,
         * - mixed
         *
         * @param string $range - The type to use when drawing
         * @return array
         *
         */
        private function range(string $range) :array{
            switch($range){
                case 'string'   : $range = array_merge(range('a', 'z'), range('A', 'Z'));break;
                case 'int'      : $range = array_merge(range(1, 9));break;
                case 'mixed'    : default : $range = array_merge(range('a', 'z'), range('A', 'Z'), range(1, 9));break;
            }

            return $range;
        }

        /**
         *
         * Rand a string from the specified range.
         * 
         * @param int $quantity - How many numbers are to be drawn
         * @param string $range - The type to use when drawing
         *
         * @return string|int
         *
         */
        public function generate(int $quantity, string $range = 'mixed'){
            $rand = '';
            $chars = array_flip($this->range($range));

            for($i = 0; $i < $quantity; $i++) $rand .= array_rand($chars);
            return $rand;
        }


        /**
         *
         * Binary draw
         *
         * @param int $quantity - How many numbers are to be drawn
         * @return string
         *
         */
        public function bytes(int $quantity) :string{
            return bin2hex(random_bytes($quantity));
        }


        /**
         *
         * Generating random numbers without repeats
         * 
         * @param int $min 		- start random
         * @param int $max 		- end random
         * @param int $quantity	- How many numbers are to be drawn
         *
         * @return array
         *
         */
        public function unique(int $min, int $max, int $quantity) :array{
            $numbers = range($min, $max);
            shuffle($numbers);
            
            return array_slice($numbers, 0, $quantity);
        }
    }