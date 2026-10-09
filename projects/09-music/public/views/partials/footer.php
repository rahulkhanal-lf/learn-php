<?php
/**
 * COMPONENT: Footer
 * Self-contained — no variables needed from outside.
 * Mirrors the Kutumba footer: Social Status, Address, Connect columns.
 */
?>

<footer class="footer" role="contentinfo">
  <div class="container">

    <div class="footer__grid">

      <!-- Column 1: Social Status -->
      <div>
        <p class="footer__heading">Social Status</p>
        <ul class="footer__list">
          <li>Social Initiatives</li>
          <li>Festival Attendants</li>
          <li>Documentry</li>
          <li>Institute</li>
        </ul>
      </div>

      <!-- Column 2: Address -->
      <div>
        <p class="footer__heading">Address</p>
        <ul class="footer__list">
          <li>Dhaugal, Lalitpur</li>
        </ul>
      </div>

      <!-- Column 3: Connect -->
      <div>
        <p class="footer__heading">Connect with Kutumba</p>
        <div class="footer__social">
          <a href="https://www.facebook.com/KutumbaNepal/" target="_blank" rel="noopener" aria-label="Facebook">f</a>
          <a href="https://twitter.com/KutumbaOfficial" target="_blank" rel="noopener" aria-label="Twitter">t</a>
          <a href="https://www.instagram.com/kutumba_nepal/" target="_blank" rel="noopener" aria-label="Instagram">in</a>
          <a href="https://www.youtube.com/user/kutumbaofficialvideo" target="_blank" rel="noopener" aria-label="YouTube">▶</a>
        </div>
        <p style="font-size:.8rem;color:var(--muted)">
          <a href="index.php?page=albums" style="color:var(--orange)">Digital Album of Kutumba Band</a>
        </p>
      </div>

    </div>

    <div class="footer__bottom">
      <!-- date('Y') — PHP function: returns current 4-digit year dynamically -->
      &copy; <?= date('Y') ?> KUTUMBA BAND. All Rights Reserved.
    </div>

  </div>
</footer>
