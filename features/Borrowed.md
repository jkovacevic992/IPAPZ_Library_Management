Admin and employees can borrow books to users, return books, print invoices. Only admin can edit other users and
promote regular users to employees. Admin can also disable and enable available payment options.

BorrowedController functions:
- borrowedBooks() returns a list of all borrowed books with information such as late fee, how many days the user is
  late, etc.
- Admin can lend books to existing users (lendBook())
- Admin can return all books (returnBooks()) or return a single book (returnSingleBook())
- Admin (and employees) can see details of borrowed books (booksDetails())
- Admin can edit borrowed books (change user, add new books, change return date...) - editBorrowed()
 