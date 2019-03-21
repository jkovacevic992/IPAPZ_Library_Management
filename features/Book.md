Books have quantity, they can belong to multiple genres.
BookController functions:

- Admin can create new books (newBook())
- Admin can edit books (editBook())
- Every user can check book details (showBook())
- Every book can have an array of images and one can be set to be the main image which is displayed next to the book on
  the landing page (setMainImage())
- Admin can delete books (deleteBook())
- listBookAction() lists all books in the library (paginated, 10 books per page)
- checkBookAvailability() checks if book is available
