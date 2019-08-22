'use strict';

$(_=> init());

let $loginForm = $('#login-form'),
    $signupForm = $('#signup-form'),
    $passwordVisibilityToggle = $('.password-visibility-toggle');

function init()
{
  $loginForm.submit(validateForm);
  $signupForm.submit(validateForm);
  $signupForm.find('input').keydown(hideValidation);
  $signupForm.find('input').on('focusout change', validateInput);

  $passwordVisibilityToggle.
    on('mousedown mouseup touchstart touchend', togglePasswordVisibility);
}

function togglePasswordVisibility()
{
  let $btn = $(this),
      $glyphicon = $btn.children('span'),
      $passwordInput = $btn.parents('.input-group').children('input');

  if ($btn.hasClass('password-hidden'))
  {
    $btn.removeClass('btn-secondary');
    $btn.removeClass('password-hidden');
    $glyphicon.removeClass('glyphicon-eye-open');

    $passwordInput.prop('type', 'text');

    $btn.addClass('btn-danger');
    $glyphicon.addClass('glyphicon-eye-close');
  }
  else
  {
    $btn.removeClass('btn-danger');
    $glyphicon.removeClass('glyphicon-eye-close');

    $passwordInput.prop('type', 'password');

    $btn.addClass('btn-secondary');
    $btn.addClass('password-hidden');
    $glyphicon.addClass('glyphicon-eye-open');
  }
}

function validateForm(event)
{
  if (this.checkValidity() === false)
  {
    event.preventDefault();
    event.stopPropagation();
    this.classList.add('was-validated');
  }
}

function hideValidation(event)
{
  let $input = $(this);
  $input.removeClass('is-invalid');
  $input.removeClass('is-valid');
}

function validateInput()
{
  let $input = $(this),
      inputText = $input.val();

  if (!inputText) return;

  let regex;

  switch ($input.attr('id'))
  {
    case 'signup-username':
      if (inputText.length > 20)
        return void(showValidationMessage(false, $input));
      regex = /^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/;
      if (!regex.test(inputText))
        return void(showValidationMessage(false, $input));
      isAvailable('username', inputText, $input);
      break;

    case 'signup-email':
      regex =
        /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
      if (!regex.test(inputText))
        return void(showValidationMessage(false, $input));
      isAvailable('email', inputText, $input);
      break;

    case 'signup-firstname':
    case 'signup-lastname':
      regex = /^[A-Za-z][A-Za-z]*(?:-[A-Za-z]+)*(?:'[A-Za-z]+)*$/;
      if (!regex.test(inputText))
        showValidationMessage(regex.test(inputText), $input);
      break;
  }
}

function isAvailable(inputType, inputText, $input)
{
  $.ajax({
    url: `${SITE_URL}user/is_valid/${inputType}`,
    data: { inputText: inputText },
    dataType: 'JSON',
    success: data => showValidationMessage(data.response, $input)
  });
}

function showValidationMessage(isValid, $input)
{
  if (isValid)
  {
    $input.removeClass('is-invalid');
    $input.addClass('is-valid');
  }
  else
  {
    $input.addClass('is-invalid');
    $input.removeClass('is-valid');
  }
}
