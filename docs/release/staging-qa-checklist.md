# Staging QA Checklist (Release Candidate)

Date: ____________  
Tester: ____________  
Build/Commit: ____________  
Environment URL: ____________

## Pre-Checks

- [ ] `php artisan migrate:fresh --seed --force` completed in staging setup
- [ ] Test data seeded and available
- [ ] Queue worker running
- [ ] Mail transport configured for staging
- [ ] `APP_ENV=staging` and debug policy confirmed

## 1) Auth & Account

- [ ] Register new **student** account
- [ ] Register new **community** account
- [ ] Login valid credentials
- [ ] Login invalid credentials shows error
- [ ] Forgot password flow sends reset mail
- [ ] Reset password with valid token
- [ ] Logout works

Pass/Fail: ____________  
Notes: ______________________________________________

## 2) Profile & Verification

- [ ] Open profile page
- [ ] Update name/email/phone successfully
- [ ] Upload profile photo
- [ ] Verification onboarding screens load
- [ ] Community doc/selfie upload works
- [ ] Student onboarding flow works

Pass/Fail: ____________  
Notes: ______________________________________________

## 3) Services (Helper Side)

- [ ] Create a service with category/title/description/price
- [ ] Edit existing service
- [ ] Service image path loads correctly
- [ ] Toggle availability/unavailability
- [ ] Service appears in listing/search/details

Pass/Fail: ____________  
Notes: ______________________________________________

## 4) Service Request Lifecycle (End-to-End)

Use two accounts: requester + provider.

- [ ] Requester creates service request
- [ ] Provider can view and **accept** request
- [ ] Provider can mark **in progress**
- [ ] Provider can mark **work finished**
- [ ] Requester confirms payment/finalize path
- [ ] Completed status visible to both sides
- [ ] Reject/cancel paths behave correctly

Pass/Fail: ____________  
Notes: ______________________________________________

## 5) Reviews & Ratings

- [ ] Requester can submit review after completion
- [ ] Provider reply to review works
- [ ] Ratings appear on relevant pages

Pass/Fail: ____________  
Notes: ______________________________________________

## 6) Notifications

- [ ] Notifications list page loads
- [ ] New notification appears for relevant action
- [ ] Mark single notification as read
- [ ] Mark all notifications as read
- [ ] Action URL redirect from notification works

Pass/Fail: ____________  
Notes: ______________________________________________

## 7) Admin Moderation

- [ ] Admin login works
- [ ] Admin can view users/services/requests
- [ ] Admin can approve/reject service
- [ ] Admin warning/block flow works
- [ ] Admin request dispute resolve path works

Pass/Fail: ____________  
Notes: ______________________________________________

## 8) Realtime / Queue / Mail Smoke

- [ ] Queue worker processes one job
- [ ] Mail notifications sent (staging mailbox)
- [ ] Realtime/chat event path (if enabled) basic smoke works

Pass/Fail: ____________  
Notes: ______________________________________________

## 9) Non-Functional Sanity

- [ ] No fatal errors in logs during QA run
- [ ] Key pages load under acceptable response time
- [ ] No broken assets/images on critical pages

Pass/Fail: ____________  
Notes: ______________________________________________

## Final Sign-Off

Overall Result:
- [ ] PASS (release candidate)
- [ ] FAIL (fix required)

Top Issues (if any):
1. ______________________________________________
2. ______________________________________________
3. ______________________________________________

Signed by: ____________________  Date: ____________
