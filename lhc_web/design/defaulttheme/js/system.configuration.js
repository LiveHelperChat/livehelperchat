(function(){
    var hash = window.location.hash;
    let tab = document.querySelector('#system-tabs a[href="' + hash.replace('#!','') + '"]');
    tab && (new bootstrap.Tab(tab)).show();

    // Configuration search functionality
    const searchInput = document.getElementById('configuration-search');
    if (searchInput) {
        const headerElement = document.getElementById('header-system-configuration');
        const searchContainer = searchInput.closest('.col-md-6');

        // Helper functions to reduce code duplication
        function resetElementsVisibility() {
            // Reset visibility for all elements
            document.querySelectorAll('#system-tabs .tab-content li').forEach(item => {
                item.style.display = '';
            });

            // Reset visibility for all UL elements
            document.querySelectorAll('#system-tabs .tab-content ul').forEach(ul => {
                ul.style.display = '';
            });
            
            // Reset visibility for all h5 elements
            document.querySelectorAll('#system-tabs .tab-content h5').forEach(header => {
                header.style.display = '';
            });

            document.querySelectorAll('#system-tabs .tab-content div').forEach(header => {
                header.style.display = '';
            });
        }

        function restoreTabFunctionality() {
            const tabPanes = document.querySelectorAll('#system-tabs .tab-content .tab-pane');
            
            // Reset all tab panes
            tabPanes.forEach(pane => {
                pane.classList.remove('active');
                pane.classList.remove('show');
            });

            // Find active tab from nav
            const activeTabLink = document.querySelector('#system-tabs .nav-tabs .nav-link.active');
            if (activeTabLink) {
                // Get the tab pane id from the active tab link
                const activeTabId = activeTabLink.getAttribute('href');
                if (activeTabId) {
                    // Find and activate the corresponding tab pane
                    const activePane = document.querySelector(activeTabId);
                    if (activePane) {
                        activePane.classList.add('active');
                        activePane.classList.add('show');
                    } else {
                        // Fallback to first tab if active pane can't be found
                        document.querySelector('#system-tabs .tab-pane:first-child').classList.add('active');
                        document.querySelector('#system-tabs .tab-pane:first-child').classList.add('show');
                    }
                }
            } else {
                // Fallback to first tab if no active tab link
                document.querySelector('#system-tabs .tab-pane:first-child').classList.add('active');
                document.querySelector('#system-tabs .tab-pane:first-child').classList.add('show');
            }

            // Show the tab nav again
            document.querySelector('#system-tabs .nav-tabs').style.display = '';
        }

        function resetColumnVisibility() {
            // Reset column visibility and restore original column size
            document.querySelectorAll('#system-tabs .tab-content .col-md-6, #system-tabs .tab-content .col-md-12').forEach(column => {
                column.style.display = '';
                // Restore original column size
                if (column.classList.contains('col-md-12')) {
                    column.classList.remove('col-md-12');
                    column.classList.add('col-md-6');
                }
            });
        }

        function manageSearchContainerSize(expand) {
            if (searchContainer) {
                if (expand) {
                    searchContainer.classList.remove('col-md-6');
                    searchContainer.classList.add('col-md-12');
                } else {
                    searchContainer.classList.remove('col-md-12');
                    searchContainer.classList.add('col-md-6');
                }
            }
        }
        
        // Hide header when search input gets focus
        searchInput.addEventListener('focus', function() {
            if (headerElement) {
                headerElement.style.display = 'none';
            }
            
            manageSearchContainerSize(true);
        });
        
        // Add escape key to lose focus
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.blur();
            }
        });
        
        // Show header when search input loses focus (only if search is empty)
        searchInput.addEventListener('blur', function() {
            // Always restore header visibility on blur
            if (this.value.toLowerCase().trim() === '' && headerElement) {
                headerElement.style.display = '';
            }

            manageSearchContainerSize(this.value.toLowerCase().trim() !== '');
        });
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const searchTermNoSpaces = searchTerm.replace(/\s+/g, '');
            const configLinks = document.querySelectorAll('#system-tabs .tab-content a');
            const configListItems = document.querySelectorAll('#system-tabs .tab-content li');
            const tabPanes = document.querySelectorAll('#system-tabs .tab-content .tab-pane');

            // Reset visibility
            resetElementsVisibility();
            resetColumnVisibility();

            if (searchTerm !== '') {
                // Show all tab panes when searching
                tabPanes.forEach(pane => {
                    pane.classList.add('active');
                    pane.classList.add('show');
                });

                // Hide the tab nav when searching
                document.querySelector('#system-tabs .nav-tabs').style.display = 'none';

                // First check if any h5 headings match the search term
                const matchingHeadings = [];
                document.querySelectorAll('#system-tabs .tab-content h5').forEach(header => {
                    const headerText = header.textContent.toLowerCase();
                    const headerTextNoSpaces = headerText.replace(/\s+/g, '');
                    if (headerText.includes(searchTerm) || headerTextNoSpaces.includes(searchTermNoSpaces)) {
                        matchingHeadings.push(header);
                    }
                });

                // Show all items under matching headings
                matchingHeadings.forEach(header => {
                    const nextUl = header.nextElementSibling;
                    if (nextUl && nextUl.tagName === 'UL') {
                        const items = nextUl.querySelectorAll('li');
                        items.forEach(item => {
                            item.style.display = '';
                        });
                    }
                });

                // Filter remaining links
                configLinks.forEach(link => {
                    const linkText = link.textContent.toLowerCase();
                    const linkTextNoSpaces = linkText.replace(/\s+/g, '');
                    const linkUrl = link.getAttribute('href') ? link.getAttribute('href').toLowerCase() : '';
                    const linkUrlNoSpaces = linkUrl.replace(/\s+/g, '');
                    const listItem = link.closest('li');

                    if (listItem) {
                        // If the item is under a matching heading, it's already set to display
                        let underMatchingHeading = false;
                        for (const header of matchingHeadings) {
                            const nextUl = header.nextElementSibling;
                            if (nextUl && nextUl.contains(listItem)) {
                                underMatchingHeading = true;
                                break;
                            }
                        }

                        if (!underMatchingHeading) {
                            if (linkText.includes(searchTerm) || linkTextNoSpaces.includes(searchTermNoSpaces) ||
                                linkUrl.includes(searchTerm) || linkUrlNoSpaces.includes(searchTermNoSpaces)) {
                                listItem.style.display = '';
                            } else {
                                listItem.style.display = 'none';
                            }
                        }
                    }
                });

                // Hide section headers and UL elements if all items in that section are hidden
                document.querySelectorAll('#system-tabs .tab-content h5').forEach(header => {
                    // If this header already matched the search term, skip it
                    if (matchingHeadings.includes(header)) {
                        header.style.display = '';
                        return;
                    }

                    const nextUl = header.nextElementSibling;
                    if (nextUl && nextUl.tagName === 'UL') {
                        const visibleItems = nextUl.querySelectorAll('li:not([style*="display: none"])').length;
                        if (visibleItems > 0) {
                            header.style.display = '';
                            nextUl.style.display = '';
                        } else {
                            header.style.display = 'none';
                            nextUl.style.display = 'none';
                        }
                    }
                });

                // Hide col-md-6 columns if they don't have any visible items
                document.querySelectorAll('#system-tabs .tab-content .col-md-6').forEach(column => {
                    const visibleItems = column.querySelectorAll('li:not([style*="display: none"])').length;
                    if (visibleItems > 0) {
                        column.style.display = '';
                        // Convert to full width during search
                        column.classList.remove('col-md-6');
                        column.classList.add('col-md-12');
                    } else {
                        column.style.display = 'none';
                    }
                });

            } else {
                restoreTabFunctionality();
                resetElementsVisibility();
            }
        });
    }
})();