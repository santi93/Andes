<?php
/*
Template Name: Contact Form
*/
?>

<?php get_header(); ?>

<section id="middle" class="contact_form">

  <?php 
    //If the form is submitted
    if(isset($_POST['submitted'])) {

    //Check to see if the honeypot captcha field was filled in
    if(trim($_POST['firstname']) !== '') {
    $captchaError = true;
    } else {

    //Check to make sure that the name field is not empty
    if(trim($_POST['contactName']) === '') {
    $nameError = 'You forgot to enter your name.';
    $hasError = true;
    } else {
    $name = trim($_POST['contactName']);
    }

    //Check to make sure sure that a valid email address is submitted
    if(trim($_POST['email']) === '')  {
    $emailError = 'You forgot to enter your email address.';
    $hasError = true;
    } else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email']))) {
    $emailError = 'You entered an invalid email address.';
    $hasError = true;
    } else {
    $email = trim($_POST['email']);
    }

    //Check to make sure that the subject field is not empty
    if(trim($_POST['subject']) === '') {
    $subjectError = 'You forgot to enter what this is in reference to.';
    $hasError = true;
    } else {
    $subject = trim($_POST['subject']);
    }

    //Check to make sure comments were entered	
    if(trim($_POST['comments']) === '') {
    $commentError = 'You forgot to enter your comments.';
    $hasError = true;
    } else {
    if(function_exists('stripslashes')) {
    $comments = stripslashes(trim($_POST['comments']));
    } else {
    $comments = trim($_POST['comments']);
    }
    }

    //If there is no error, send the email
    if(!isset($hasError)) {

    $emailTo = '';
    $subject = 'Contact Form Submission from '.$name;
    $sendCopy = trim($_POST['sendCopy']);
    $body = "Name: $name \n\nEmail: $email \n\nComments: $comments";
    $headers = 'From: My Site <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;

    mail($emailTo, $subject, $body, $headers);

    if($sendCopy == true) {
    $subject = 'You emailed Santiago';
    $headers = 'From: Dsblox <>';
    mail($email, $subject, $body, $headers);
    }

    $emailSent = true;

    }
      }
        }

  ?>


  <?php if(isset($emailSent) && $emailSent == true) { ?>

  <div class="thanks">
    <h1>Thanks, <?=$name;?></h1>
    <p>Your email was successfully sent. I will be in touch soon.</p>
  </div>

  <?php } else { ?>

  <?php if (have_posts()) : ?>

  <?php while (have_posts()) : the_post(); ?>

  <?php the_content(); ?>



  <form action="<?php the_permalink(); ?>" id="contactForm" method="post">

    <ol class="forms">

      <li>
        <label for="contactName" id="name"> Name </label> 
        <?php if($nameError != '') { ?>
        <span class="error"> <?=$nameError;?> </span> 
        <?php } ?>  <br />
        <input type="text" name="contactName" id="contactName" value="<?php if(isset($_POST['contactName'])) echo $_POST['contactName'];?>" class="requiredField" />			
      </li>
	
      <li>
        <label for="email"> Email </label>
        <?php if($emailError != '') { ?>
        <span class="error"><?=$emailError;?></span>
        <?php } ?>
        <input type="text" name="email" id="email" value="<?php if(isset($_POST['email']))  echo $_POST['email'];?>" class="requiredField email" />
      </li>

      <li>
        <label for="subject"> Subject </label> 
        <?php if($subjectError != '') { ?>
        <span class="error"><?=$subjectError;?></span> 
        <?php } ?> <br />
        <input type="text" name="subject" id="subject" value="<?php if(isset($_POST['subject'])) echo $_POST['subject'];?>" class="requiredField" />
      </li>		

      <li class="textarea">
        <label for="commentsText"> Comments </label>
        <textarea name="comments" id="commentsText" rows="20" cols="30" class="requiredField">
          <?php if(isset($_POST['comments'])) { if(function_exists('stripslashes')) { echo stripslashes($_POST['comments']); } else { echo $_POST['comments']; } } 
          ?>
        </textarea>
        <?php if($commentError != '') { ?>
        <span class="error"> <?=$commentError;?> </span> 
        <?php } ?>
        </li>
      
    </ol>

    <div class="inline">
    
      <input type="checkbox" name="sendCopy" id="sendCopy" value="true"<?php if(isset($_POST['sendCopy']) && $_POST['sendCopy'] == true) echo ' checked="checked"'; ?> />
      <label id="sendcopy" for="sendCopy"> Send a copy of this email to yourself </label>
    </div>

    <span class="buttons">
      <input type="submit" name="submitted" id="submitted" value="Send" />
    </span>
    
    <span class="firstname">
      <label for="firstname" class="firstname"> First Name</label>
      <input type="text" name="firstname" id="firstname" class="firstname" value="<?php if(isset($_POST['firstname']))  echo $_POST['firstname'];?>" />
    </span>

  </form>




  <?php endwhile; ?>
  <?php endif; ?>
  <?php } ?>

</section>

<?php get_footer(); ?>