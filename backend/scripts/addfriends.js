
    window.addEventListener('DOMContentLoaded', () => {
        // Get references to the search box and results div
        const searchBox = document.querySelector('.search-box input[type="text"]');
        const searchResults = document.querySelector('.search-results');

        // List of user names to be searched
        const users = ['User 1', 'User 2', 'User 3'];

        // Function to handle adding a friend
        const addFriend = (user) => {
            alert(`You have sent ${user} a friend request.`);
        }

        // Add event listener to the search button
        document.querySelector('.search-box button').addEventListener('click', () => {
            // Simulate a search request (replace this with your actual search code)
            const searchText = searchBox.value.trim();
            if (searchText !== '') {
                const resultsHtml = `
                    <h2>Search Results for "${searchText}"</h2>
                    <ul>
                        ${users
                            .filter(user => user.toLowerCase().includes(searchText.toLowerCase()))
                            .map(user => `
                                <li><span>${user}</span>
                                    <button onclick="addFriend('${user}')">Add Friend</button>
                                </li>
                            `)
                            .join('')
                        }
                    </ul>
                `;
                searchResults.innerHTML = resultsHtml;
                searchResults.style.display = 'block';
            } else {
                searchResults.style.display = 'none';
            }
        });
    });
