<?php if ($error): ?>
  <div class="k-error-details" style="margin-bottom: 2em;">
    <dl>
      <dt><?= Escape::html($error); ?></dt>
    </dl>
  </div>
<?php endif; ?>

<header class="k-field-header">
  <div class="k-field-label">Sign in with</div>
</header>

<ul class="k-list k-draggable" data-size="auto">
  <?php foreach($providers as $provider): ?>
    <li class="k-list-item">
      <a class="k-link k-list-item-content" href="<?= $baseUrl . '/' . $provider->getId() ?>">
        <div class="k-list-item-text"><?= $provider->getName() ?></div>
      </a>
      <nav class="k-list-item-options">
        <a href="<?= $baseUrl . '/' . $provider->getId() ?>" class="k-button">
          <span aria-hidden="true" class="k-button-icon k-icon k-icon-check"><svg viewBox="0 0 16 16"><use xlink:href="#icon-check"></use></svg></span>
        </a>
      </nav>
    </li>
  <?php endforeach; ?>
</ul>
