#!/bin/bash
# Integration Test Script for Laravel JWT Admin Panel

API_URL="http://localhost:8000/api"
ADMIN_EMAIL="ravinder@possibilitysolutions.com"
ADMIN_PASSWORD="123456"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}========================================${NC}"
echo -e "${YELLOW}Laravel JWT Admin Panel - Integration Tests${NC}"
echo -e "${YELLOW}========================================${NC}\n"

# Test 1: User Registration
echo -e "${YELLOW}[TEST 1] User Registration${NC}"
REGISTRATION_RESPONSE=$(curl -s -X POST "$API_URL/register" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Integration Test User",
    "email": "test_'$(date +%s)'@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }')

if echo "$REGISTRATION_RESPONSE" | grep -q "User registered successfully"; then
  echo -e "${GREEN}✓ User registration successful${NC}"
  TEST_EMAIL=$(echo "$REGISTRATION_RESPONSE" | grep -o '"email":"[^"]*' | cut -d'"' -f4)
else
  echo -e "${RED}✗ User registration failed${NC}"
  echo "$REGISTRATION_RESPONSE"
fi

# Test 2: User Login
echo -e "\n${YELLOW}[TEST 2] User Login${NC}"
USER_LOGIN=$(curl -s -X POST "$API_URL/login" \
  -H "Content-Type: application/json" \
  -d "{
    \"email\": \"$TEST_EMAIL\",
    \"password\": \"password123\"
  }")

if echo "$USER_LOGIN" | grep -q "Login successful"; then
  echo -e "${GREEN}✓ User login successful${NC}"
  USER_TOKEN=$(echo "$USER_LOGIN" | grep -o '"token":"[^"]*' | cut -d'"' -f4)
else
  echo -e "${RED}✗ User login failed${NC}"
fi

# Test 3: Admin Login
echo -e "\n${YELLOW}[TEST 3] Admin Login${NC}"
ADMIN_LOGIN=$(curl -s -X POST "$API_URL/login" \
  -H "Content-Type: application/json" \
  -d "{
    \"email\": \"$ADMIN_EMAIL\",
    \"password\": \"$ADMIN_PASSWORD\"
  }")

if echo "$ADMIN_LOGIN" | grep -q "Login successful"; then
  echo -e "${GREEN}✓ Admin login successful${NC}"
  ADMIN_TOKEN=$(echo "$ADMIN_LOGIN" | grep -o '"token":"[^"]*' | cut -d'"' -f4)
else
  echo -e "${RED}✗ Admin login failed${NC}"
fi

# Test 4: Get Current User
echo -e "\n${YELLOW}[TEST 4] Get Current User (Protected)${NC}"
ME=$(curl -s -X GET "$API_URL/me" \
  -H "Authorization: Bearer $USER_TOKEN")

if echo "$ME" | grep -q "success"; then
  echo -e "${GREEN}✓ Get current user successful${NC}"
else
  echo -e "${RED}✗ Get current user failed${NC}"
fi

# Test 5: Get All Users (Admin Only)
echo -e "\n${YELLOW}[TEST 5] Get All Users (Admin Only)${NC}"
USERS=$(curl -s -X GET "$API_URL/users" \
  -H "Authorization: Bearer $ADMIN_TOKEN")

if echo "$USERS" | grep -q "success"; then
  echo -e "${GREEN}✓ Get all users successful${NC}"
  USER_COUNT=$(echo "$USERS" | grep -o '"id"' | wc -l)
  echo -e "  Total users: $USER_COUNT"
else
  echo -e "${RED}✗ Get all users failed${NC}"
fi

# Test 6: Get Single User (Admin Only)
echo -e "\n${YELLOW}[TEST 6] Get Single User (Admin Only)${NC}"
SINGLE_USER=$(curl -s -X GET "$API_URL/users/2" \
  -H "Authorization: Bearer $ADMIN_TOKEN")

if echo "$SINGLE_USER" | grep -q "success"; then
  echo -e "${GREEN}✓ Get single user successful${NC}"
else
  echo -e "${RED}✗ Get single user failed${NC}"
fi

# Test 7: Edit User (Admin Only)
echo -e "\n${YELLOW}[TEST 7] Edit User (Admin Only)${NC}"
EDIT_USER=$(curl -s -X PUT "$API_URL/users/2" \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated User Name",
    "email": "updated_'$(date +%s)'@example.com"
  }')

if echo "$EDIT_USER" | grep -q "User updated successfully"; then
  echo -e "${GREEN}✓ Edit user successful${NC}"
else
  echo -e "${RED}✗ Edit user failed${NC}"
fi

# Test 8: Block User (Admin Only)
echo -e "\n${YELLOW}[TEST 8] Block User (Admin Only)${NC}"
BLOCK=$(curl -s -X POST "$API_URL/users/2/block" \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json")

if echo "$BLOCK" | grep -q "User blocked successfully"; then
  echo -e "${GREEN}✓ Block user successful${NC}"
else
  echo -e "${RED}✗ Block user failed${NC}"
fi

# Test 9: Unblock User (Admin Only)
echo -e "\n${YELLOW}[TEST 9] Unblock User (Admin Only)${NC}"
UNBLOCK=$(curl -s -X POST "$API_URL/users/2/unblock" \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json")

if echo "$UNBLOCK" | grep -q "User unblocked successfully"; then
  echo -e "${GREEN}✓ Unblock user successful${NC}"
else
  echo -e "${RED}✗ Unblock user failed${NC}"
fi

# Test 10: Logout
echo -e "\n${YELLOW}[TEST 10] Logout${NC}"
LOGOUT=$(curl -s -X POST "$API_URL/logout" \
  -H "Authorization: Bearer $USER_TOKEN")

if echo "$LOGOUT" | grep -q "Logged out successfully"; then
  echo -e "${GREEN}✓ Logout successful${NC}"
else
  echo -e "${RED}✗ Logout failed${NC}"
fi

# Test 11: Unauthorized Access (Non-Admin trying to access admin endpoint)
echo -e "\n${YELLOW}[TEST 11] Authorization Check (Non-Admin)${NC}"
NEW_LOGIN=$(curl -s -X POST "$API_URL/login" \
  -H "Content-Type: application/json" \
  -d "{
    \"email\": \"$TEST_EMAIL\",
    \"password\": \"password123\"
  }")
NEW_USER_TOKEN=$(echo "$NEW_LOGIN" | grep -o '"token":"[^"]*' | cut -d'"' -f4)

UNAUTHORIZED=$(curl -s -X GET "$API_URL/users" \
  -H "Authorization: Bearer $NEW_USER_TOKEN")

if echo "$UNAUTHORIZED" | grep -q "Forbidden"; then
  echo -e "${GREEN}✓ Authorization check working (non-admin denied)${NC}"
else
  echo -e "${RED}✗ Authorization check failed${NC}"
fi

# Test 12: Blocked User Cannot Login
echo -e "\n${YELLOW}[TEST 12] Blocked User Cannot Login${NC}"
# First, block the test user
curl -s -X POST "$API_URL/users/2/block" \
  -H "Authorization: Bearer $ADMIN_TOKEN" \
  -H "Content-Type: application/json" > /dev/null

# Try to login with blocked account
BLOCKED_LOGIN=$(curl -s -X POST "$API_URL/login" \
  -H "Content-Type: application/json" \
  -d "{
    \"email\": \"$TEST_EMAIL\",
    \"password\": \"password123\"
  }")

if echo "$BLOCKED_LOGIN" | grep -q "blocked"; then
  echo -e "${GREEN}✓ Blocked user correctly denied login${NC}"
else
  echo -e "${RED}✗ Blocked user check failed${NC}"
fi

echo -e "\n${YELLOW}========================================${NC}"
echo -e "${YELLOW}Integration Tests Completed${NC}"
echo -e "${YELLOW}========================================${NC}\n"
