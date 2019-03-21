PayPalTransactionController functions:

- paypalDisplay() - renders a page where the user can see the amount they have to pay via paypal and paypal form.
- payment() - executes the payment and stores transaction info in the database
- gateway() - creates new BraintreeGateway
- calculateLateFeeBorrowed() - calculates late fee
- premiumMembership() - executes paypal transaction for premium membership (users can't do anything after they
register until they pay for premium membership.) Premium membership status is checked every day at 0.00 using cron.
