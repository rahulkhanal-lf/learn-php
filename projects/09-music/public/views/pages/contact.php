<?php
/**
 * PAGE: Contact
 *
 * PHP CONCEPTS HERE:
 *  - $_SERVER['REQUEST_METHOD'] — detect GET vs POST
 *  - Form validation with $errors array
 *  - PDO INSERT via Contact model
 *  - Session flash messages (success after redirect)
 *  - PRG pattern: Post → Redirect → Get (prevents form resubmission)
 *  - filter_var() for email validation
 *  - htmlspecialchars() to repopulate form fields safely
 */

use App\Models\Contact;

session_start();

// Read and clear flash message (set by PRG redirect below)
$flash  = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$errors = [];
$fields = ['name' => '', 'email' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CONCEPT: Sanitize input at the boundary (user input = untrusted).
    // trim() removes leading/trailing whitespace.
    // htmlspecialchars() at OUTPUT time (not here) prevents XSS.
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate
    if (strlen($name) < 2) {
        $errors['name'] = 'Full name is required (min 2 characters).';
    }

    // filter_var() — PHP built-in validator for common formats.
    // FILTER_VALIDATE_EMAIL checks RFC-compliant email format.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if (strlen($message) < 10) {
        $errors['message'] = 'Message must be at least 10 characters.';
    }

    if (empty($errors)) {

        // Save to DB via Contact model (PDO prepared statement)
        (new Contact())->save([
            'name'    => $name,
            'email'   => $email,
            'message' => $message,
        ]);

        /*
         * CONCEPT: PRG Pattern (Post / Redirect / Get)
         * After a successful POST, redirect to the same page (GET).
         * This prevents "Do you want to resubmit?" on browser refresh.
         *
         * Flash message: store success in session, read after redirect.
         * $_SESSION persists across redirects (same browser session).
         */
        $_SESSION['flash'] = ['type' => 'success', 'text' => 'Thank you! Your message has been sent.'];
        header('Location: index.php?page=contact');
        exit;

    } else {
        // Repopulate fields so user doesn't lose their input
        $fields = compact('name', 'email', 'message');
    }
}

$pageTitle   = 'Contact';
$currentPage = 'contact';

ob_start();
?>

<div class="page-banner">
  <div class="container">
    <h1>Contact</h1>
  </div>
</div>

<section class="section">
  <div class="container">

    <div class="contact-layout">

      <!-- Map -->
      <div class="contact-map">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3533.4!2d85.3178!3d27.6669!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjfCsDQwJzAwLjgiTiA4NcKwMTknMDQuMSJF!5e0!3m2!1sen!2snp!4v1234567890"
          width="100%"
          height="400"
          style="border:0;display:block"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          title="Kutumba location"
        ></iframe>
      </div>

      <!-- Form -->
      <div class="contact-form">
        <h2>Send a Message</h2>

        <?php if ($flash): ?>
          <div class="alert alert-<?= $flash['type'] ?>">
            <?= htmlspecialchars($flash['text'], ENT_QUOTES, 'UTF-8') ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=contact" novalidate>

          <div class="form-group">
            <label for="name">Full Name</label>
            <input
              type="text"
              id="name"
              name="name"
              value="<?= htmlspecialchars($fields['name'], ENT_QUOTES, 'UTF-8') ?>"
              placeholder="Full Name"
              required
            >
            <?php if (isset($errors['name'])): ?>
              <span style="color:#ff4444;font-size:.8rem">
                <?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?>
              </span>
            <?php endif; ?>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <input
              type="email"
              id="email"
              name="email"
              value="<?= htmlspecialchars($fields['email'], ENT_QUOTES, 'UTF-8') ?>"
              placeholder="Email"
              required
            >
            <?php if (isset($errors['email'])): ?>
              <span style="color:#ff4444;font-size:.8rem">
                <?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?>
              </span>
            <?php endif; ?>
          </div>

          <div class="form-group">
            <label for="message">Message</label>
            <textarea
              id="message"
              name="message"
              placeholder="Your message..."
              required
            ><?= htmlspecialchars($fields['message'], ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php if (isset($errors['message'])): ?>
              <span style="color:#ff4444;font-size:.8rem">
                <?= htmlspecialchars($errors['message'], ENT_QUOTES, 'UTF-8') ?>
              </span>
            <?php endif; ?>
          </div>

          <button type="submit" class="btn btn-orange">Send Message</button>

        </form>
      </div>

    </div>
  </div>
</section>

<?php
$content = ob_get_clean();
require BASE_PATH . '/views/layout.php';
