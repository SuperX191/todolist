document.addEventListener('DOMContentLoaded', function() {
  // Initialize note dragging functionality
  initNoteDragging();
  
  // Double click to create new note
  const noteBoard = document.getElementById('note-board');
  if (noteBoard) {
    noteBoard.addEventListener('dblclick', function(e) {
      if (e.target === noteBoard) {
        createNewNote(e.clientX - noteBoard.getBoundingClientRect().left, 
                     e.clientY - noteBoard.getBoundingClientRect().top);
      }
    });
  }
  
  // Format chat timestamps
  formatChatTimestamps();
});

function initNoteDragging() {
  document.querySelectorAll('.note').forEach(note => {
    let isDragging = false;
    let offsetX = 0;
    let offsetY = 0;
    const noteId = note.dataset.id;
    const board = note.closest('.note-board');

    note.addEventListener('mousedown', e => {
      if (e.target.classList.contains('note-delete')) return;
      isDragging = true;
      offsetX = e.clientX - note.getBoundingClientRect().left;
      offsetY = e.clientY - note.getBoundingClientRect().top;
      note.style.zIndex = 999;
      note.style.cursor = 'grabbing';
    });

    document.addEventListener('mousemove', e => {
      if (!isDragging) return;
      const boardRect = board.getBoundingClientRect();
      const x = e.clientX - boardRect.left - offsetX;
      const y = e.clientY - boardRect.top - offsetY;
      
      // Boundary checks
      const maxX = boardRect.width - note.offsetWidth;
      const maxY = boardRect.height - note.offsetHeight;
      
      note.style.left = Math.max(0, Math.min(x, maxX)) + 'px';
      note.style.top = Math.max(0, Math.min(y, maxY)) + 'px';
    });

    document.addEventListener('mouseup', () => {
      if (!isDragging) return;
      isDragging = false;
      note.style.zIndex = 1;
      note.style.cursor = 'move';

      const x = parseInt(note.style.left);
      const y = parseInt(note.style.top);

      // Update position on server
      updateNotePosition(noteId, x, y);
    });

    // Edit note on double click
    note.addEventListener('dblclick', function(e) {
      if (e.target.classList.contains('note-delete')) return;
      const contentElement = note.querySelector('.note-content');
      const currentContent = contentElement.textContent.trim();
      const newContent = prompt('Edit your note:', currentContent);
      
      if (newContent !== null && newContent !== currentContent) {
        contentElement.textContent = newContent;
        updateNoteContent(noteId, newContent);
      }
    });
  });
}

function createNewNote(x, y) {
  const content = prompt('Enter your note content:');
  if (!content) return;
  
  const color = prompt('Choose color (yellow, pink, blue):', 'yellow');
  if (!['yellow', 'pink', 'blue'].includes(color)) {
    alert('Invalid color! Using default (yellow)');
    color = 'yellow';
  }
  
  // In a real app, you would send this to the server via AJAX
  // For now, we'll just simulate it
  fetch('add_note.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `content=${encodeURIComponent(content)}&color=${color}&pattern=`
  })
  .then(response => response.text())
  .then(() => {
    window.location.reload();
  });
}

function updateNotePosition(id, x, y) {
  fetch('update_note.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ id, pos_x: x, pos_y: y })
  });
}

function updateNoteContent(id, content) {
  fetch('update_note.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ id, content })
  });
}

function formatChatTimestamps() {
  const messages = document.querySelectorAll('.message-info');
  let currentDate = '';
  
  messages.forEach(msg => {
    const timestamp = msg.textContent;
    const dateObj = new Date(timestamp);
    const messageDate = dateObj.toLocaleDateString();
    
    if (messageDate !== currentDate) {
      currentDate = messageDate;
      const dayDivider = document.createElement('div');
      dayDivider.className = 'day-divider';
      
      const dayName = dateObj.toLocaleDateString(undefined, { weekday: 'long' });
      dayDivider.innerHTML = `<span>${dayName}, ${messageDate}</span>`;
      
      msg.parentElement.parentElement.insertBefore(dayDivider, msg.parentElement);
    }
    
    // Format time only
    msg.textContent = dateObj.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
  });
}