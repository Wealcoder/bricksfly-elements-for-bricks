import AffiliateProgram from "@/components/dashboard/AffiliateProgram";
import AiConnect from "@/components/dashboard/AiConnect";
import Documentation from "@/components/dashboard/Documentation";
import HeroBanner from "@/components/dashboard/HeroBanner";
import LatestBlog from "@/components/dashboard/LatestBlog";
import Tutorial from "@/components/dashboard/Tutorial";
import VideoBanner from "@/components/dashboard/VideoBanner";
import WhatsNew from "@/components/dashboard/WhatsNew";
import SplitRow from "@/components/shared/SplitRow";

const Dashboard = () => {

  return (
    <div className="flex flex-col gap-6">
      <SplitRow columns={[60, 40]}>
        <HeroBanner />
        <VideoBanner />
      </SplitRow>

      <SplitRow columns={[60, 40]}>
        <Tutorial />
        <Documentation />
      </SplitRow>
      {/* In the middle while the feature is new; moves to the top when it is final. */}
      <AiConnect />
      <SplitRow columns={[50, 50]}>
        <AffiliateProgram />
        <WhatsNew />
      </SplitRow>
      <LatestBlog />
    </div>
  );
};

export default Dashboard;

