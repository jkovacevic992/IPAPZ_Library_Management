Users can have multiple roles (admin, employee, user, premium user) and depending on what their role is, they
have different permissions. Regular users can't do anything. Premium users can add books to their wish list,
reserve them if they're not available and pay their late fee via PayPal.


UserController functions:
- login, register, logout functions
- users() - shows registered users who can be promoted to employees (only admin can promote them)
- employees() - shows a list of employees
- viewUser() - user details where admin can edit user info or delete user
- editUser() - edit user info
- deleteUser() - delete user (can't delete if they have any books)
- makeEmployee() - promote user to employee
- usersBorrowedBooks() - user can see a list of their borrowed books (they can also pay their late fee via PayPal on
the same page)
- addToWishlist() - users can add any book to their wish list which they can see when they login.
- removeFromWishlist() - remove book from wishlist
- reserveBook() - user can reserve a book if it is currently not available (all copies of the book are borrowed)
- calculateLateFee() - calculates late fee for a single book.
