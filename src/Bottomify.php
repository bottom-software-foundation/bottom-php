<?php

namespace NCPlayz\Bottomify;

use TypeError;

/**
 *  A sample class
 *
 *  Use this section to define what this class is doing, the PHPDocumentator will use this
 *  to automatically generate an API documentation using this information.
 *
 *  @author yourname
 */
class Bottomify
{
   private const CHARACTER_VALUES = [
      200 => "🫂",
      50  => "💖",
      10  => "✨",
      5   => "🥺",
      1   => ",",
      0   => "❤️",
   ];

   private const SECTION_SEPERATOR = "👉👈";

   public function encodeChar(int $charValue)
   {
      if ($charValue == 0) {
         return "";
      }
      $currentCase = (function () use ($charValue) {
         foreach (Bottomify::CHARACTER_VALUES as $value => $character) {
            if ($charValue >= $value) {
               return $character;
            }
         }
         return Bottomify::CHARACTER_VALUES[0];
      })();

      $rest = $this->encodeChar($charValue - array_search($currentCase, Bottomify::CHARACTER_VALUES));
      return "{$currentCase}{$rest}";
   }

   public function encode(string $value)
   {
      return implode(Bottomify::SECTION_SEPERATOR, array_map(array($this, 'encodeChar'), array_map('ord', str_split($value)))) . Bottomify::SECTION_SEPERATOR;
   }

   public function decode(string $value)
   {
      return implode("", array_map('chr', array_filter(array_map(function (string $chars) {
         return (function (string $chars) {
            $index = 0;
            $ord = 0;
            while ($index < strlen($chars)) {
               $remaining = substr($chars, $index);
               $recognised = false;
               $recognisedChar = "";
               foreach (Bottomify::CHARACTER_VALUES as $value => $character) {
                  if (str_starts_with($remaining, $character)) {
                     $recognised = true;
                     $ord += $value;
                     $recognisedChar = $character;
                     break;
                  }
               }
               if (!$recognised) {
                  throw new TypeError("Invalid bottom text: '$chars'");
               }
               $index += strlen($recognisedChar);
            }
            return $ord;
         })($chars);
      }, explode(Bottomify::SECTION_SEPERATOR, preg_replace('/{Bottomify::SECTION_SEPARATOR}?$/', "", trim($value)))), function ($var) { return $var != 0; })));
   }
}
