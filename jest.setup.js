// Add any global setup or mocks needed for your tests
// For example, you can add global mocks or polyfills here

// Mock browser APIs that might not be available in the test environment
global.CustomEvent = class CustomEvent {
  constructor(type, eventInitDict) {
    this.type = type;
    this.detail = eventInitDict ? eventInitDict.detail : {};
  }
};

// Fallback for APIs missing in JSDOM
if (!global.window) {
  global.window = {
    addEventListener: jest.fn(),
    removeEventListener: jest.fn(),
    requestAnimationFrame: jest.fn(cb => cb()),
    cancelAnimationFrame: jest.fn(),
    performance: {
      now: jest.fn().mockReturnValue(0)
    }
  };
}

if (!global.document) {
  global.document = {
    addEventListener: jest.fn(),
    removeEventListener: jest.fn(),
    createElement: jest.fn(),
    querySelectorAll: jest.fn(),
  };
}