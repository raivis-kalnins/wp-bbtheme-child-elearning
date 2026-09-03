(function () {
  'use strict';
  var cfg = window.wpbbLmsAdmin || {};

  function updateOrder(root) {
    var input = root.querySelector('[data-wpbb-lms-order]');
    if (!input) return;
    input.value = JSON.stringify(Array.prototype.map.call(root.querySelectorAll('[data-wpbb-lms-list] > li'), function (item) {
      return parseInt(item.getAttribute('data-id'), 10) || 0;
    }).filter(Boolean));
    var empty = root.querySelector('.wpbb-lms-builder__empty');
    if (empty) empty.hidden = !!root.querySelector('[data-wpbb-lms-list] > li');
  }

  function initBuilder(root) {
    var list = root.querySelector('[data-wpbb-lms-list]');
    if (!list) return;
    var dragging = null;
    list.addEventListener('dragstart', function (event) {
      dragging = event.target.closest('li');
      if (dragging) dragging.classList.add('is-dragging');
    });
    list.addEventListener('dragend', function () {
      if (dragging) dragging.classList.remove('is-dragging');
      dragging = null;
      updateOrder(root);
    });
    list.addEventListener('dragover', function (event) {
      if (!dragging) return;
      event.preventDefault();
      var target = event.target.closest('li');
      if (!target || target === dragging) return;
      var box = target.getBoundingClientRect();
      list.insertBefore(dragging, event.clientY < box.top + box.height / 2 ? target : target.nextSibling);
    });

    root.addEventListener('click', function (event) {
      var button = event.target.closest('[data-wpbb-lms-add]');
      if (!button) return;
      var type = button.getAttribute('data-wpbb-lms-add');
      var title = window.prompt(type === 'course_quiz' ? cfg.promptQuiz : cfg.promptLesson, '');
      if (title === null) return;
      button.disabled = true;
      var old = button.textContent;
      button.textContent = cfg.creating || 'Creating...';
      var body = new URLSearchParams({ action: 'wpbb_lms_quick_add', nonce: cfg.nonce || '', course_id: root.getAttribute('data-course-id') || '', type: type, title: title });
      window.fetch(cfg.ajaxUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }, body: body.toString() })
        .then(function (response) { return response.json(); })
        .then(function (response) {
          if (!response || !response.success) throw new Error('failed');
          var item = response.data;
          var li = document.createElement('li');
          li.draggable = true;
          li.setAttribute('data-id', item.id);
          li.setAttribute('data-type', item.type);
          li.innerHTML = '<span class="dashicons dashicons-menu" aria-hidden="true"></span><div><strong></strong><small></small></div><a class="button-link" href=""></a>';
          li.querySelector('strong').textContent = item.title;
          li.querySelector('small').textContent = item.typeLabel + ' · Draft';
          li.querySelector('a').href = item.editUrl;
          li.querySelector('a').textContent = 'Edit';
          list.appendChild(li);
          updateOrder(root);
        })
        .catch(function () { window.alert(cfg.failed || 'The item could not be created.'); })
        .finally(function () { button.disabled = false; button.textContent = old; });
    });
  }

  function questionTemplate(index) {
    return '<section class="wpbb-lms-question-row" data-question-index="' + index + '">' +
      '<div class="wpbb-lms-question-row__head"><strong>Question ' + (index + 1) + '</strong><button type="button" class="button-link-delete" data-wpbb-lms-remove-question>Remove</button></div>' +
      '<p><label>Question<textarea class="widefat" name="wpbb_lms_questions[' + index + '][question]" rows="2"></textarea></label></p>' +
      '<div class="wpbb-lms-choice-grid">' + ['a','b','c','d'].map(function (letter) { return '<label><span>' + letter.toUpperCase() + '</span><input class="widefat" name="wpbb_lms_questions[' + index + '][choices][' + letter + ']" value=""></label>'; }).join('') + '</div>' +
      '<div class="wpbb-lms-question-row__foot"><label>Correct answer<select name="wpbb_lms_questions[' + index + '][correct]"><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option></select></label><label>Explanation<input class="widefat" name="wpbb_lms_questions[' + index + '][explanation]" value=""></label></div></section>';
  }

  function initQuestions(root) {
    var holder = root.querySelector('[data-wpbb-lms-questions]');
    if (!holder) return;
    root.addEventListener('click', function (event) {
      if (event.target.closest('[data-wpbb-lms-add-question]')) {
        var index = holder.querySelectorAll('.wpbb-lms-question-row').length;
        holder.insertAdjacentHTML('beforeend', questionTemplate(index));
      }
      var remove = event.target.closest('[data-wpbb-lms-remove-question]');
      if (remove) {
        var row = remove.closest('.wpbb-lms-question-row');
        if (row && holder.querySelectorAll('.wpbb-lms-question-row').length > 1) row.remove();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.wpbb-lms-builder').forEach(initBuilder);
    document.querySelectorAll('[data-wpbb-lms-question-builder]').forEach(initQuestions);
  });
})();
