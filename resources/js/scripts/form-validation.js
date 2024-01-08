/*
 * Form Validation
 */
$(function () {

  $('select[required]').css({
    position: 'absolute',
    display: 'inline',
    height: 0,
    padding: 0,
    border: '1px solid rgba(255,255,255,0)',
    width: 0
  });

  $("#formValidate").validate({
    rules: {
      uname: {
        required: true,
        minlength: 5
      },
      cemail: {
        required: true,
        email: true
      },
      password: {
        required: true,
        minlength: 5
      },
      cpassword: {
        required: true,
        minlength: 5,
        equalTo: "#password"
      },
      crole: {
        required: true,
      },
      curl: {
        required: true,
        url: true
      },
      ccomment: {
        required: true,
        minlength: 15
      },
      tnc_select: "required",
    },
    //For custom messages
    messages: {
      uname: {
        required: "Enter a username",
        minlength: "Enter at least 5 characters"
      },
      curl: "Enter your website",
    },
    errorElement: 'div',
    errorPlacement: function (error, element) {
      var placement = $(element).data('error');
      if (placement) {
        $(placement).append(error)
      } else {
        error.insertAfter(element);
      }
    }
  });
  $("#formValidateCompany").validate({
    rules: {
      company_name: {
        required: true
      },
      company_code: {
        required: true
      },
      address1: {
        required: true
      },
      country: {
        required: true
      },
      state: {
        required: true
      },
      city: {
        required: true
      },
      pincode: {
        required: true,
      },
      licence_valid_till: {
        required: true
      },
      contact_person: {
        required: true
      },
      contact_mobile: {
        required: true
      },
    },
    //For custom messages
    messages: {
      uname: {
        required: "Enter a username",
        minlength: "Enter at least 5 characters"
      },
      curl: "Enter your website",
    },
    errorElement: 'div',
    errorPlacement: function (error, element) {
      var placement = $(element).data('error');
      if (placement) {
        $(placement).append(error)
      } else {
        error.insertAfter(element);
      }
    }
  });
});