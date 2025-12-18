// User Support JavaScript

document.addEventListener('DOMContentLoaded', function() {
    console.log('User Support JS loaded');
    
    const searchInput = document.getElementById('faqSearch');
    const searchBtn = document.getElementById('searchFaqBtn');
    const faqResults = document.getElementById('faqResults');
    const faqSuggestionsList = document.getElementById('faqSuggestionsList');
    const faqAnswer = document.getElementById('faqAnswer');
    const faqAnswerContent = document.getElementById('faqAnswerContent');
    
    if (!searchInput || !faqSuggestionsList) {
        console.error('Required elements not found');
        return;
    }
    
    let searchTimeout;

    // FAQ Toggle functionality
    const faqQuestions = document.querySelectorAll('.faq-question');
    console.log('Found FAQ questions:', faqQuestions.length);
    
    faqQuestions.forEach((question, index) => {
        question.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('FAQ question clicked:', index);
            
            const faqItem = this.closest('.faq-item');
            if (!faqItem) {
                console.error('FAQ item not found');
                return;
            }
            
            const isActive = faqItem.classList.contains('active');
            console.log('Is active:', isActive);
            
            // Close all other FAQ items
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem) {
                    item.classList.remove('active');
                }
            });
            
            // Toggle current item
            if (isActive) {
                faqItem.classList.remove('active');
            } else {
                faqItem.classList.add('active');
            }
            
            console.log('FAQ item active state:', faqItem.classList.contains('active'));
        });
    });

    // Search FAQ on input
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    searchFaq(query);
                }, 300);
            } else {
                hideResults();
            }
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim();
                if (query.length >= 2) {
                    searchFaq(query);
                }
            }
        });
    }

    // Search button click
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query.length >= 2) {
                searchFaq(query);
            }
        });
    }

    function searchFaq(query) {
        if (!query || query.length < 2) {
            hideResults();
            hideAnswer();
            return;
        }

        console.log('Searching for:', query);
        
        const url = `/user/support/search-faq?q=${encodeURIComponent(query)}`;
        console.log('Fetching:', url);

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Search results:', data);
                if (data.results && data.results.length > 0) {
                    displaySuggestions(data.results);
                    hideAnswer();
                } else {
                    hideResults();
                    hideAnswer();
                    // Show message that no results found
                    if (faqAnswer && faqAnswerContent) {
                        faqAnswerContent.innerHTML = '<p style="color: #6b7280;">' + (document.dir === 'rtl' ? 'لم يتم العثور على نتائج' : 'No results found') + '</p>';
                        faqAnswer.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                console.error('Error searching FAQ:', error);
                hideResults();
                hideAnswer();
                if (faqAnswer && faqAnswerContent) {
                    faqAnswerContent.innerHTML = '<p style="color: #ef4444;">' + (document.dir === 'rtl' ? 'حدث خطأ أثناء البحث' : 'An error occurred while searching') + '</p>';
                    faqAnswer.style.display = 'block';
                }
            });
    }

    function displaySuggestions(results) {
        if (!faqSuggestionsList) {
            console.error('faqSuggestionsList element not found');
            return;
        }
        
        faqSuggestionsList.innerHTML = '';
        
        results.forEach(result => {
            const item = document.createElement('div');
            item.className = 'faq-suggestion-item';
            const arrowDir = document.documentElement.dir === 'rtl' ? 'left' : 'right';
            item.innerHTML = `
                <span class="faq-suggestion-text">${escapeHtml(result.question)}</span>
                <i class="fa-solid fa-chevron-${arrowDir}"></i>
            `;
            item.addEventListener('click', function() {
                showAnswer(result.question, result.answer);
                hideResults();
                // Clear search input
                if (searchInput) {
                    searchInput.value = '';
                }
            });
            faqSuggestionsList.appendChild(item);
        });
        
        if (faqResults) {
            faqResults.style.display = 'block';
        }
    }

    function showAnswer(question, answer) {
        if (!faqAnswer || !faqAnswerContent) {
            console.error('Answer elements not found');
            return;
        }
        
        faqAnswerContent.innerHTML = escapeHtml(answer).replace(/\n/g, '<br>');
        faqAnswer.style.display = 'block';
        
        // Scroll to answer box smoothly
        setTimeout(() => {
            faqAnswer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    }

    function hideResults() {
        faqResults.style.display = 'none';
        faqSuggestionsList.innerHTML = '';
    }

    function hideAnswer() {
        faqAnswer.style.display = 'none';
    }

    function showNoResults() {
        // Optionally show a "no results" message
        hideAnswer();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});

