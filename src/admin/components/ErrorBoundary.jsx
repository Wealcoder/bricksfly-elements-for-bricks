import { Component } from "react";

/**
 * Keeps a rendering error in one screen from leaving the whole admin page
 * blank. Shows the error message (useful for support) and a reload button.
 */
export default class ErrorBoundary extends Component {
  constructor(props) {
    super(props);
    this.state = { error: null };
  }

  static getDerivedStateFromError(error) {
    return { error };
  }

  componentDidCatch(error, info) {
    // eslint-disable-next-line no-console
    console.error("BricksFly admin error:", error, info?.componentStack);
  }

  render() {
    const { error } = this.state;

    if (!error) {
      return this.props.children;
    }

    return (
      <div
        role="alert"
        style={{
          margin: "40px auto",
          maxWidth: 640,
          padding: 24,
          background: "#fff",
          border: "1px solid #dcdcde",
          borderRadius: 8,
          fontSize: 14,
        }}
      >
        <p style={{ fontSize: 16, fontWeight: 600, margin: "0 0 8px" }}>
          This BricksFly screen could not be loaded.
        </p>
        <p style={{ margin: "0 0 12px" }}>
          Please reload the page. If the problem continues, send the message
          below to BricksFly support.
        </p>
        <pre
          style={{
            whiteSpace: "pre-wrap",
            background: "#f6f7f7",
            padding: 12,
            borderRadius: 4,
            margin: "0 0 16px",
          }}
        >
          {String(error?.message || error)}
        </pre>
        <button
          type="button"
          className="button button-primary"
          onClick={() => window.location.reload()}
        >
          Reload page
        </button>
      </div>
    );
  }
}
