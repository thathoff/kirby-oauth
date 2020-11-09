<template>
  <div>
    <div
      v-if="error !== null"
      class="k-error-details"
      style="margin-bottom: 2em;"
    >
      <dl>
        <dt>{{ error }}</dt>
      </dl>
    </div>

    <header class="k-field-header">
      <div class="k-field-label">Sign in with</div>
    </header>

    <ul class="k-list k-draggable" data-size="auto">
      <li
        v-for="provider in providers"
        :key="provider.id"
        :id="`thathoff-oauth-${provider.id}`"
        class="k-list-item"
      >
        <a class="k-link k-list-item-content" :href="provider.href">
          <div class="k-list-item-text">{{ provider.name }}</div>
        </a>
        <nav class="k-list-item-options">
          <a :href="provider.href" class="k-button">
            <span aria-hidden="true" class="k-button-icon k-icon k-icon-check">
              <svg
                xmlns:xlink="http://www.w3.org/1999/xlink"
                viewBox="0 0 16 16"
              >
                <use xlink:href="#icon-check"></use>
              </svg>
            </span>
          </a>
        </nav>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    providers: {
      type: Array,
      default: []
    },
    error: {
      type: String,
      default: null
    }
  }
};
</script>
