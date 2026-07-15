<?php

/*
TraceFusion functionality
*/

//===========================================================================
//
// Input:  arbitrary format of TraceFusion data - array.
// Output:
//    $text_format==FALSE => binary encrypted sequence/string
//    $text_format==TRUE  => TEXTurized encrypted sequence/string

function MWX__TraceFusion_PrepareTraceFusionSignature ($tracefusion_data, $crypto_key, $text_format=FALSE)
{
   $final_data = $tracefusion_data;

//{{{PHP_DO_NOT_ENCODE}}}
   // Content of protection buffer
   // --------------------------------
   // + Encrypted data = N bytes
   // + CRC32_plain of plain data                        = 4 bytes
   // + CRC32_enc of encrypted data                      = 4 bytes
   // + SIZE of everything above, XOR-ed with CRC32_enc  = 4 bytes
   //
   $final_data = serialize ($tracefusion_data);                // Flatten array to string
   $final_data = gzcompress($final_data, 5);                   // Compress result

   // Calculate plain CRC
   $crc32_plain = substr (md5($final_data), -8);               // Calculate CRC or result
   $crc32_plain = pack ("H*", $crc32_plain);

   $final_data = MWX__TraceFusion_encrypt_data ($final_data, $crypto_key);   // Encrypt result

   // Calculate encrypted CRC
   $crc32_enc = substr (md5($final_data), -8);                 // Calculate CRC or result
   $crc32_enc = pack ("H*", $crc32_enc);

   // Append CRC's
   $final_data .= $crc32_plain;
   $final_data .= $crc32_enc;

   $total_size = strlen ($final_data);                         // Calculate total size
   $total_size = sprintf ("%08X", $total_size);
   $total_size = pack ("H*", $total_size);

   // Encrypt total size value.
   $total_size[0] = $total_size[0] ^ $crc32_enc[0];
   $total_size[1] = $total_size[1] ^ $crc32_enc[1];
   $total_size[2] = $total_size[2] ^ $crc32_enc[2];
   $total_size[3] = $total_size[3] ^ $crc32_enc[3];
   $final_data .= $total_size;                                 // Append total size to result

   if ($text_format)
      {
      $final_data = unpack ("H*", $final_data);
      $final_data = $final_data[1];
      }
//{{{/PHP_DO_NOT_ENCODE}}}

   return $final_data;
}
//===========================================================================

//===========================================================================
function MWX__TraceFusion_encrypt_data ($data, $key) { return MWX__TraceFusion_encrypt_decrypt_data ($data, $key, TRUE);  }
function MWX__TraceFusion_decrypt_data ($data, $key) { return MWX__TraceFusion_encrypt_decrypt_data ($data, $key, FALSE); }

function MWX__TraceFusion_encrypt_decrypt_data ($data, $key, $is_encrypt)
{
   $new_data = $data;
//{{{PHP_DO_NOT_ENCODE}}}
   $data_len = strlen($new_data);

   $new_key = md5($key);
   $new_key = pack ("H*", $new_key);
   $key_len  = strlen($new_key);


   if (!$is_encrypt)
      {
      // Decrypting
      for ($i=$data_len-2; $i>0; $i--)
         $new_data[$i] = ($new_data[$i] ^ $new_data[$i-1]) ^ $new_data[$i+1];
      }

   // XOR beginning of data with key
   for ($i=0; $i<$data_len && $i<$key_len; $i++)
      $new_data[$i] = $new_data[$i] ^ $new_key[$i];

   // Reverse string
   $new_data = strrev ($new_data);

   // XOR beginning of reversed data with key again.
   for ($i=0; $i<$data_len && $i<$key_len; $i++)
      $new_data[$i] = $new_data[$i] ^ $new_key[$i];

   // Slide-XOR string on itself to make it all changed.
   if ($is_encrypt)
      {
      // Encrypting
      for ($i=1; $i<($data_len-1); $i++)
         $new_data[$i] = ($new_data[$i] ^ $new_data[$i-1]) ^ $new_data[$i+1];
      }
//{{{/PHP_DO_NOT_ENCODE}}}

   return $new_data;
}
//===========================================================================

?>