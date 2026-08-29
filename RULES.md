# Rentiva — RULES.md

## Marketplace
1. Never trust client-side prices.
2. Never trust client-side availability.
3. Never trust client-side ownership.
4. Never trust payment callbacks without server verification.
5. Financial transitions are auditable.
6. Booking state transitions are explicit.
7. A request is not an active rental until acceptance conditions are met.
8. Prevent overlapping active reservations.
9. Historical prices use snapshots.
10. Private tenant information is never public.
11. Owners manage only owned/assigned properties.
12. Admin access uses policies/permissions.

## Data
13. Money uses integer minor units.
14. Use a consistent timezone strategy.
15. Slugs are unique.
16. Mandatory relationships use foreign keys.
17. Financial records are never hard-deleted.
18. Private files are not public.
19. Minimize personal data.
20. Logs contain no secrets.

## CMS
21. Homepage content is admin-editable.
22. Navigation is admin-editable.
23. SEO defaults are admin-editable.
24. Content and design remain separate.
25. No arbitrary CSS editor.
26. Reference websites are feature inspiration only.

## Code
27. Follow Laravel conventions.
28. Use Form Requests for complex validation.
29. Use Policies for authorization.
30. Use Actions/Services for workflows.
31. No complex business logic in Blade.
32. No transaction logic inside Filament form definitions.
33. Use events/notifications for decoupling.
34. Queue slow work.
35. Avoid N+1 queries.
36. Paginate large datasets.
37. Eager-load intentionally.
38. Add indexes for real query patterns.
39. Prefer readable code.
40. Avoid premature abstraction.

## Security
41. Verify webhook signatures.
42. Make webhooks idempotent.
43. Rate-limit sensitive endpoints.
44. Validate upload MIME type and size.
45. Sanitize rich text.
46. Escape public output.
47. Never expose stack traces publicly.
48. Use CSRF protection.
49. Store secrets in environment/secret management.
50. Re-check authorization whenever relationships change.

## Testing
51. Every critical booking rule has a test.
52. Every payment transition has a test.
53. Every private resource has authorization tests.
54. Every publishing state has a test.
55. Run tests before completing tasks.
