/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
// Open on click functionality.
function closeSubmenus(element) {
  element.querySelectorAll('[aria-expanded="true"]').forEach(function (toggle) {
    toggle.setAttribute('aria-expanded', 'false');
  });
}

function toggleSubmenuOnClick(event) {
  const buttonToggle = event.target.closest('[aria-expanded]');
  const isSubmenuOpen = buttonToggle.getAttribute('aria-expanded');

  if (isSubmenuOpen === 'true') {
    closeSubmenus(buttonToggle.closest('.gc-block-navigation-item'));
  } else {
    // Close all sibling submenus.
    const parentElement = buttonToggle.closest('.gc-block-navigation-item');
    const navigationParent = buttonToggle.closest('.gc-block-navigation__submenu-container, .gc-block-navigation__container, .gc-block-page-list');
    navigationParent.querySelectorAll('.gc-block-navigation-item').forEach(function (child) {
      if (child !== parentElement) {
        closeSubmenus(child);
      }
    }); // Open submenu.

    buttonToggle.setAttribute('aria-expanded', 'true');
  }
} // Necessary for some themes such as TT1 Blocks, where
// scripts could be loaded before the body.


window.addEventListener('load', () => {
  const submenuButtons = document.querySelectorAll('.gc-block-navigation-submenu__toggle');
  submenuButtons.forEach(function (button) {
    button.addEventListener('click', toggleSubmenuOnClick);
  }); // Close on click outside.

  document.addEventListener('click', function (event) {
    const navigationBlocks = document.querySelectorAll('.gc-block-navigation');
    navigationBlocks.forEach(function (block) {
      if (!block.contains(event.target)) {
        closeSubmenus(block);
      }
    });
  }); // Close on focus outside or escape key.

  document.addEventListener('keyup', function (event) {
    const submenuBlocks = document.querySelectorAll('.gc-block-navigation-item.has-child');
    submenuBlocks.forEach(function (block) {
      if (!block.contains(event.target)) {
        closeSubmenus(block);
      } else if (event.key === 'Escape') {
        const toggle = block.querySelector('[aria-expanded="true"]');
        closeSubmenus(block); // Focus the submenu trigger so focus does not get trapped in the closed submenu.

        toggle?.focus();
      }
    });
  });
});

/******/ })()
;