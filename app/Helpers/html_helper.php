<?php

if (! function_exists('script_tag'))
{
  /**
   * Script
   *
   * Generates link to a JS file
   *
   * @param  mixed   $src       Script source or an array
   * @param  boolean $indexPage Should indexPage be added to the JS path
   * @return string
   */
  function script_tag($src = '', bool $indexPage = false): string
  {
    $script = '<script ';
    if (! is_array($src))
    {
      $src = ['src' => $src];
    }

    foreach ($src as $k => $v)
    {
      if ($k === 'src' && ! preg_match('#^([a-z]+:)?//#i', $v))
      {
        $v = "assets/js/{$v}.js";

        if ($indexPage === true)
        {
          $script .= 'src="' . site_url($v) . '" ';
        }
        else
        {
          $script .= 'src="' . slash_item('baseURL') . $v . '" ';
        }
      }
      else
      {
        $script .= $k . '="' . $v . '" ';
      }
    }

    return $script . 'type="text/javascript"' . '></script>';
  }
}

// ------------------------------------------------------------------------

if (! function_exists('link_tag'))
{
  /**
   * Link
   *
   * Generates link to a CSS file
   *
   * @param  mixed   $href      Stylesheet href or an array
   * @param  string  $rel
   * @param  string  $type
   * @param  string  $title
   * @param  string  $media
   * @param  boolean $indexPage should indexPage be added to the CSS path.
   * @return string
   */
  function link_tag($href = '', string $rel = 'stylesheet', string $type = 'text/css', string $title = '', string $media = '', bool $indexPage = false): string
  {
    $link = '<link ';

    // extract fields if needed
    if (is_array($href))
    {
      $rel       = $href['rel'] ?? $rel;
      $type      = $href['type'] ?? $type;
      $title     = $href['title'] ?? $title;
      $media     = $href['media'] ?? $media;
      $indexPage = $href['indexPage'] ?? $indexPage;
      $href      = $href['href'] ?? '';
    }

    if (! preg_match('#^([a-z]+:)?//#i', $href))
    {
      $href = "assets/css/{$href}.css";

      if ($indexPage === true)
      {
        $link .= 'href="' . site_url($href) . '" ';
      }
      else
      {
        $link .= 'href="' . slash_item('baseURL') . $href . '" ';
      }
    }
    else
    {
      $link .= 'href="' . $href . '" ';
    }

    $link .= 'rel="' . $rel . '" type="' . $type . '" ';

    if ($media !== '')
    {
      $link .= 'media="' . $media . '" ';
    }

    if ($title !== '')
    {
      $link .= 'title="' . $title . '" ';
    }

    return $link . '/>';
  }
}

// ------------------------------------------------------------------------