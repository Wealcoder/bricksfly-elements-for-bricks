import { useState } from "react";
import { toast } from "sonner";
import { cn } from "@/lib/utils";
import { buttonVariants } from "../ui/button";
import { Input } from "../ui/input";
import { Label } from "../ui/label";
import { Textarea } from "../ui/textarea";

const initialForm = { name: "", email: "", feature: "" };

const RequestFeatureForm = () => {
  const [form, setForm] = useState(initialForm);
  const [submitting, setSubmitting] = useState(false);

  const handleChange = (field) => (e) => {
    setForm((prev) => ({ ...prev, [field]: e.target.value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setSubmitting(true);

    try {
      const response = await fetch(BRICKSFLY_ADDONS_ADMIN.ajaxurl, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          Accept: "application/json",
        },
        body: new URLSearchParams({
          action: "bricksfly_request_new_feature",
          name: form.name,
          email: form.email,
          feature: form.feature,
          nonce: BRICKSFLY_ADDONS_ADMIN.nonce,
        }),
      }).then((res) => res.json());

      if (response?.success) {
        toast.success(
          response.data || "Thanks! Your feature request has been submitted.",
          { position: "top-right" },
        );
        setForm(initialForm);
      } else {
        toast.error(
          response?.data || "Something went wrong. Please try again.",
          { position: "top-right" },
        );
      }
    } catch (error) {
      toast.error("Something went wrong. Please try again.", {
        position: "top-right",
      });
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="border rounded-2xl p-5 shadow-common h-full">
      <p className="text-lg font-medium text-text">Request New Feature</p>
      <form onSubmit={handleSubmit} className="flex flex-col gap-6 mt-6">
        <div className="flex flex-col sm:flex-row sm:items-center gap-6">
          <div className="flex flex-col gap-3 flex-1">
            <Label htmlFor="feature-name" className="text-xs text-text">
              Name
            </Label>
            <Input
              id="feature-name"
              placeholder="Your name"
              className="bg-background-secondary"
              value={form.name}
              onChange={handleChange("name")}
              required
            />
          </div>
          <div className="flex flex-col gap-3 flex-1">
            <Label htmlFor="feature-email" className="text-xs text-text">
              Mail Address
            </Label>
            <Input
              id="feature-email"
              type="email"
              placeholder="example@mail.com"
              className="bg-background-secondary"
              value={form.email}
              onChange={handleChange("email")}
              required
            />
          </div>
        </div>
        <div className="flex flex-col gap-3">
          <Label htmlFor="feature-idea" className="text-xs text-text">
            Feature Idea
          </Label>
          <Textarea
            id="feature-idea"
            placeholder="Feature description"
            className="min-h-[215px] resize-none bg-background-secondary"
            value={form.feature}
            onChange={handleChange("feature")}
            required
          />
        </div>
        <button
          type="submit"
          disabled={submitting}
          className={cn(buttonVariants({ variant: "secondary" }), "w-fit")}
        >
          Submit
          <img
            src={`${BRICKSFLY_ADDONS_ADMIN.plugin_url}public/images/submit-icon.png`}
            alt=""
            className="w-3 h-3 ms-1"
          />
        </button>
      </form>
    </div>
  );
};

export default RequestFeatureForm;
