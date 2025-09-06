module.exports = {
  testEnvironment: 'jsdom',
  transform: {
    '^.+\\.js$': 'babel-jest'
  },
  moduleFileExtensions: ['js'],
  testMatch: ['**/tests/js/**/*.test.js'],
  setupFilesAfterEnv: ['<rootDir>/jest.setup.js'],
  collectCoverageFrom: ['public/js/**/*.js'],
  coverageReporters: ['text', 'lcov'],
  coverageDirectory: 'coverage'
};