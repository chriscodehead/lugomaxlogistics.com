// LUGOMAX LOGISTICS - MASTER JAVASCRIPT

// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
  const mobileToggle = document.querySelector('.mobile-toggle');
  const navMenu = document.querySelector('.nav-menu');
  
  if (mobileToggle && navMenu) {
    mobileToggle.addEventListener('click', function() {
      navMenu.classList.toggle('active');
      
      // Animate toggle icon
      const spans = this.querySelectorAll('span');
      if (navMenu.classList.contains('active')) {
        spans[0].style.transform = 'rotate(45deg) translateY(8px)';
        spans[1].style.opacity = '0';
        spans[2].style.transform = 'rotate(-45deg) translateY(-8px)';
      } else {
        spans.forEach(span => {
          span.style.transform = '';
          span.style.opacity = '';
        });
      }
    });
  }
  
  // Close mobile menu when clicking outside
  document.addEventListener('click', function(e) {
    if (navMenu && !e.target.closest('.nav-container')) {
      navMenu.classList.remove('active');
      const spans = mobileToggle.querySelectorAll('span');
      spans.forEach(span => {
        span.style.transform = '';
        span.style.opacity = '';
      });
    }
  });
});

// Smooth Scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const href = this.getAttribute('href');
    if (href !== '#') {
      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    }
  });
});

// Animated Counter for Stats
function animateCounter(element, target) {
  let current = 0;
  const increment = target / 100;
  const timer = setInterval(() => {
    current += increment;
    if (current >= target) {
      element.textContent = target.toLocaleString() + '+';
      clearInterval(timer);
    } else {
      element.textContent = Math.floor(current).toLocaleString();
    }
  }, 20);
}

// Intersection Observer for animations
const observerOptions = {
  threshold: 0.2,
  rootMargin: '0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('fade-in-up');
      
      // Trigger counter animation for stats
      if (entry.target.classList.contains('stat-item')) {
        const h3 = entry.target.querySelector('h3');
        const targetValue = parseInt(h3.textContent.replace(/[^0-9]/g, ''));
        if (targetValue) {
          animateCounter(h3, targetValue);
        }
      }
      
      observer.unobserve(entry.target);
    }
  });
}, observerOptions);

// Observe all cards, features, and stats
document.addEventListener('DOMContentLoaded', () => {
  const animatedElements = document.querySelectorAll('.card, .feature-box, .stat-item, .service-detail, .article-card, .product-card');
  animatedElements.forEach(el => observer.observe(el));
});

// FAQ Accordion
document.querySelectorAll('.faq-question').forEach(question => {
  question.addEventListener('click', function() {
    const faqItem = this.parentElement;
    const isActive = faqItem.classList.contains('active');
    
    // Close all FAQ items
    document.querySelectorAll('.faq-item').forEach(item => {
      item.classList.remove('active');
    });
    
    // Open clicked item if it wasn't active
    if (!isActive) {
      faqItem.classList.add('active');
    }
  });
});

// Form Validation
function validateForm(formId) {
  const form = document.getElementById(formId);
  if (!form) return;
  
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        isValid = false;
        field.style.borderColor = '#dc3545';
      } else {
        field.style.borderColor = '';
      }
    });
    
    // Email validation
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (field.value && !emailRegex.test(field.value)) {
        isValid = false;
        field.style.borderColor = '#dc3545';
      }
    });
    
    if (isValid) {
      // Show success message
      alert('Form submitted successfully! We will get back to you soon.');
      form.reset();
    } else {
      alert('Please fill in all required fields correctly.');
    }
  });
}

// Initialize form validation
document.addEventListener('DOMContentLoaded', () => {
  validateForm('contact-form');
  validateForm('quote-form');
  validateForm('application-form');
});

// Filter functionality for Blog
document.querySelectorAll('.filter-tag').forEach(tag => {
  tag.addEventListener('click', function() {
    // Remove active class from all tags
    document.querySelectorAll('.filter-tag').forEach(t => t.classList.remove('active'));
    // Add active class to clicked tag
    this.classList.add('active');
    
    const filter = this.textContent.toLowerCase();
    const articles = document.querySelectorAll('.article-card');
    
    articles.forEach(article => {
      if (filter === 'all posts') {
        article.style.display = 'block';
      } else {
        const category = article.querySelector('.article-category');
        if (category && category.textContent.toLowerCase().includes(filter)) {
          article.style.display = 'block';
        } else {
          article.style.display = 'none';
        }
      }
    });
  });
});

// Search functionality
const searchInput = document.querySelector('.search-bar input');
if (searchInput) {
  searchInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const searchableItems = document.querySelectorAll('.article-card, .resource-card, .faq-item');
    
    searchableItems.forEach(item => {
      const text = item.textContent.toLowerCase();
      if (text.includes(searchTerm)) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    });
  });
}

// Tracking Form
const trackingForm = document.getElementById('tracking-form');
if (trackingForm) {
  trackingForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const trackingNumber = document.getElementById('tracking-number').value;
    
    if (trackingNumber) {
      // Show loading
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.textContent;
      submitBtn.textContent = 'Searching...';
      submitBtn.disabled = true;
      
      // Simulate API call
      setTimeout(() => {
        alert(`Tracking information for ${trackingNumber}:
        
Status: In Transit
Location: Manchester Distribution Center
Expected Delivery: Tomorrow by 5:00 PM
        
You will receive updates via email and SMS.`);
        
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
      }, 1500);
    }
  });
}

// Quote Form Multi-Step
let currentStep = 1;
const totalSteps = 3;

function showStep(step) {
  document.querySelectorAll('.form-step').forEach((stepEl, index) => {
    if (index + 1 === step) {
      stepEl.style.display = 'block';
    } else {
      stepEl.style.display = 'none';
    }
  });
  
  // Update step indicators
  document.querySelectorAll('.step-circle').forEach((circle, index) => {
    if (index + 1 <= step) {
      circle.classList.add('active');
      circle.nextElementSibling.classList.add('active');
    } else {
      circle.classList.remove('active');
      circle.nextElementSibling.classList.remove('active');
    }
  });
}

function nextStep() {
  if (currentStep < totalSteps) {
    currentStep++;
    showStep(currentStep);
  }
}

function prevStep() {
  if (currentStep > 1) {
    currentStep--;
    showStep(currentStep);
  }
}

// File Upload Preview
const fileUpload = document.querySelector('.file-upload');
if (fileUpload) {
  const fileInput = fileUpload.querySelector('input[type="file"]');
  
  fileUpload.addEventListener('click', () => {
    fileInput.click();
  });
  
  fileInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
      const fileName = this.files[0].name;
      const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2);
      fileUpload.querySelector('p').innerHTML = `
        <strong>${fileName}</strong><br>
        ${fileSize} MB
      `;
    }
  });
}

// Add active class to current page in navigation
document.addEventListener('DOMContentLoaded', () => {
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';
  const navLinks = document.querySelectorAll('.nav-menu a');
  
  navLinks.forEach(link => {
    if (link.getAttribute('href') === currentPage) {
      link.classList.add('active');
    }
  });
});

// Lazy loading for images
if ('IntersectionObserver' in window) {
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.classList.remove('lazy');
        imageObserver.unobserve(img);
      }
    });
  });

  const lazyImages = document.querySelectorAll('img.lazy');
  lazyImages.forEach(img => imageObserver.observe(img));
}

// Newsletter Form
const newsletterForm = document.querySelector('.newsletter-form');
if (newsletterForm) {
  newsletterForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const email = this.querySelector('input[type="email"]').value;
    
    if (email) {
      alert(`Thank you for subscribing! We've sent a confirmation email to ${email}`);
      this.reset();
    }
  });
}

// Scroll to top button (if exists)
const scrollTopBtn = document.querySelector('.scroll-top');
if (scrollTopBtn) {
  window.addEventListener('scroll', () => {
    if (window.pageYOffset > 300) {
      scrollTopBtn.style.display = 'flex';
    } else {
      scrollTopBtn.style.display = 'none';
    }
  });
  
  scrollTopBtn.addEventListener('click', () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
}

// Add ripple effect to buttons
document.querySelectorAll('.btn').forEach(button => {
  button.addEventListener('click', function(e) {
    const ripple = document.createElement('span');
    const rect = this.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.classList.add('ripple');
    
    this.appendChild(ripple);
    
    setTimeout(() => ripple.remove(), 600);
  });
});

// Console greeting
console.log('%cLugomax Logistics', 'color: #FF6B2C; font-size: 24px; font-weight: bold;');
console.log('%cYour Trusted Courier & Logistics Partner', 'color: #0A1F44; font-size: 14px;');
